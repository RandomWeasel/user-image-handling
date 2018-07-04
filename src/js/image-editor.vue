<template>

    <div>

        <div class="button-edit" v-on:click="showPopover = true">Edit</div>

        <div v-if="showPopover" class="popover-imageEditor" :id="image.id">


            <slot name="title">
                <div class="title">Edit Image</div>
            </slot>


            <form class="form-commonstyle content">

                <slot name="fields-default">

                </slot>


                <div class="col-half">
                    <div class="popover-image">

                        <div class="image" :style=" 'background-image:url(' + image.path + versionedImage + '); transform: rotate(' +  imageData.rotation + 'deg)' "></div>

                        <!--<img :src="'/img/property-images/' + image.filename" alt="" :style="'transform: rotate(' +  imageData.rotation + 'deg)'">-->

                        <image-rotation :image-id="image.id"></image-rotation>

                        <div class="form-row dates">
                            <span>Created at: {{createdAt}}</span>
                            <span>Updated at: {{updatedAt}}</span>
                        </div>
                    </div>
                </div>


                <div class="col-half fields">

                    <slot name="categories" :categories="categories" :model="imageData">
                    </slot>

                    <field-errors :errorObject="errors.category_id"></field-errors>


                    <slot name="fields-default">
                        <div class="form-row">

                            <slot name="label_is_primary">
                                <label for="is_primary" class="label-long">
                                    Make Primary Image:
                                </label>
                            </slot>

                            <input type="checkbox" v-model="imageData.is_primary">


                            <field-errors :errorObject="errors.is_primary"></field-errors>

                        </div>

                        <div class="form-row">

                            <slot name="label_is_shown">
                                <label for="is_shown" class="label-long">
                                    Show this Image:
                                </label>
                            </slot>

                            <input type="checkbox" v-model="imageData.is_shown">

                            <field-errors :errorObject="errors.is_shown"></field-errors>
                        </div>


                        <div class="form-row">

                            <slot name="label_caption">
                                <label for="caption" class="label-long">
                                    Image Caption:
                                </label>
                            </slot>

                            <textarea name="caption" v-model="imageData.caption"></textarea>

                            <field-errors :errorObject="errors.caption"></field-errors>

                            <slot name="subtext_caption"></slot>


                        </div>
                    </slot>


                    <slot name="fields-extra"></slot>


                </div>


                <div class="form-row">

                    <div class="form-col-half">
                        <div class="button-cancel" v-on:click="cancelImageEdits">Cancel Editing (close without saving)</div>
                    </div>

                    <div class="form-col-half">

                        <div class="button-save" v-on:click="submitImageEdits">
                            <slot name="text_submit">Save Changes</slot>
                        </div>
                    </div>
                </div>


                <div class="form-row">
                    <div class="message-formSuccess" v-if="success == true">
                        {{statusMessage}}
                    </div>
                    <div class="message-formError" v-if="success == 'failed'">
                        {{statusMessage}}
                    </div>
                    <div class="message-formError" v-if="Object.keys(errors).length !=  0">
                        There was an error submitting the form - please see  messages in the relevant section of the page
                    </div>
                </div>


            </form>






        </div>

    </div>





</template>

<script type="text/ecmascript-6">

    import utilities from './utilities.js';

    export default {
        data()
    {
        return {
//            token: window.,
            imageData: Object.assign({}, this.image),
            showPopover: false,
            success: '',
            statusMessage: '',
            errors: {},
            versionedImage: this.image.filename //initial value - no version number
        }
    },

    mounted() {

        //listener for updated image data (when an edit is saved)
        bus.$on('updatedImageData', function(updatedImageData){

            //if the edited image was set to be the primary image, remove this from all other editors
            if(updatedImageData.is_primary == true){

                if(this.imageData.id != updatedImageData.id){
                    this.imageData.is_primary = false;
                }

            }

        });

        //set default rotation value to prevent errors
//        thisVue.imageData.rotation = 0;

        //listener for rotation data
        //add the rotation value to imageData
        bus.$on('rotation', (rotationData) => {

            var imageId = rotationData[0];
            var rotation = rotationData[1];

            //check if was this image being rotated
            if(imageId == this.imageData.id){
                this.$set(this.imageData, 'rotation', rotation);
            }

        })
    },

    props: [
        'image',
        'categories',
        'postUrl',
    ],

    computed: {

        createdAt (){
            return new Date(this.image.created_at).toLocaleDateString('en-GB');
        },
        updatedAt (){
            return new Date(this.image.updated_at).toLocaleDateString('en-GB');
        }
    },

    watch: {

        //if an image is made primary, automatically mark as shown
        'imageData.is_primary': function(val){
            if(this.imageData.is_primary == true){
                this.imageData.is_shown = true;
            }
        },

        //if an image is marked not shown, automatically set is_primary to false
        'imageData.is_shown': function(val){
            if(this.imageData.is_shown == false){
                this.imageData.is_primary = false;
            }
        }
    },

    methods: {
        cancelImageEdits: function(){
            this.showPopover = false;

            //clear the edits made to the data
            //by replacing with the original data
//            this.imageData = Object.assign({}, this.image);

        },

        makeVersionedImage: function(){
            var timestamp = Math.random();
            return this.imageData.filename + '?v=' + timestamp;
        },

        submitImageEdits() {
//            console.log('submit clicked!');

            //clear errors
            this.errors = {};

            //post the data
            fetch(this.postUrl + '/' +  this.image.id, {
                method: 'post',
                headers: this.$fetchHeaders,
                credentials: 'same-origin',
                body: JSON.stringify(this.imageData) //the array of data to submit

            }).then(utilities.promiseStatus) //function in interactions.js to check for 404 type errors

                    .then((response) => {
                        return response.json()
                    }).then((json) => {

                        if(json.success == 'true'){
                            this.success = true;
                            this.statusMessage = json.message;


                            utilities.resetSuccess(this, 1000);

                            //emit a success event to be caught by a parent
                            bus.$emit(
                                    "updatedImageData", this.imageData
                            );

                            //close the modal
                            this.showPopover = false;


                            //create a new versioned image to force a reload
                            this.versionedImage = this.makeVersionedImage();

                            //remove the rotation value to start fresh
                            this.imageData.rotation = 0;

                        } else {
                            //did not save successfully -set the errors data
                            this.errors = json.errors;
                        }


                    }).catch((error) => {
                        //if the promise is rejected, this runs.
                        //Set success = false, show error, etc
                        console.log('Request Failed: ' + error);

                        this.success = 'failed';
                        this.statusMessage = 'Not Saved: ' + error;

                        utilities.resetSuccess(this, 2000);

                    });
        }
    }

    }


</script>