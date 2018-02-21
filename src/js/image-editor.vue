<template>

    <div>

        <div class="button-edit" v-on:click="showPopover = true">Edit</div>

        <div v-if="showPopover" class="popover-imageEditor" :id="image.id">


            <slot name="title">
                <div class="title">Edit Image</div>
            </slot>


            <!--{!! Form::model($image, ['route' => ['propertyImageUpdate', $image->id], 'method' => 'put', 'class' => 'form-commonstyle content']) !!}-->

            <form class="form-commonstyle content">


                <div class="col-half">
                    <div class="popover-image">

                        <div class="image" :style=" 'background-image:url(/img/property-images/' + versionedImage + '); transform: rotate(' +  imageData.rotation + 'deg)' "></div>

                        <!--<img :src="'/img/property-images/' + image.filename" alt="" :style="'transform: rotate(' +  imageData.rotation + 'deg)'">-->

                        <image-rotation :image-id="image.id"></image-rotation>

                        <div class="form-row dates">
                            <span>Created at: {{createdAt}}</span>
                            <span>Updated at: {{updatedAt}}</span>
                        </div>
                    </div>
                </div>


                <div class="col-half fields">

                    <slot name="categories" :options="categories" :model="imageData">
                    </slot>

                    <field-errors :errorObject="errors.category_id"></field-errors>


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

<script>

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

    mounted: function() {

        var thisVue = this;

        //listener for updated image data (when an edit is saved)
        bus.$on('updatedImageData', function(updatedImageData){

            //if the edited image was set to be the primary image, remove this from all other editors
            if(updatedImageData.is_primary == true){

                if(thisVue.imageData.id != updatedImageData.id){
                    thisVue.imageData.is_primary = false;
                }

            }

            //if it was this image which was edited
            if(updatedImageData.id == thisVue.imageData.id){

                //create a new versioned image to force a reload
                thisVue.versionedImage = thisVue.makeVersionedImage();

                //remove the rotation value to start fresh
                thisVue.imageData.rotation = 0;
            }

        });

        //set default rotation value to prevent errors
//        thisVue.imageData.rotation = 0;

        //listener for rotation data
        //add the rotation value to imageData
        bus.$on('rotation', function(rotationData){

            var imageId = rotationData[0];
            var rotation = rotationData[1];

            //check if was this image being rotated
            console.log('image being edited ' + imageId);

            if(imageId == thisVue.imageData.id){
                console.log('thisimage ' + thisVue.imageData.id);
                console.log(rotation);
                thisVue.$set(thisVue.imageData, 'rotation', rotation);
            }

        })
    },

    props: [
        'image',
        'categories',
        'postUrl'
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
            console.log('new versioned image image-editor');
            return this.imageData.filename + '?v=' + timestamp;
        },

        submitImageEdits: function(){
            console.log('submit clicked!');
            var thisVue = this;

            //clear errors
            thisVue.errors = {};

            //post the data
            fetch(thisVue.postUrl, {
                method: 'put',
                headers: this.$fetchHeaders,
                credentials: 'same-origin',
                body: JSON.stringify(thisVue.imageData) //the array of data to submit

            }).then(promiseStatus) //function in interactions.js to check for 404 type errors

                    .then(function(response){
                        return response.json()
                    }).then(function(json){

                        if(json.success == 'true'){
                            thisVue.success = true;
                            thisVue.statusMessage = json.message;

                            resetSuccess(thisVue, 1000);

                            //emit a success event to be caught by a parent
                            bus.$emit(
                                    "updatedImageData", thisVue.imageData
                            );

                            //close the modal
                            thisVue.showPopover = false;
                        } else {
                            //did not save successfully -set the errors data
                            thisVue.errors = json.errors;
                        }


                    }).catch(function(error){
                        //if the promise is rejected, this runs.
                        //Set success = false, show error, etc
                        console.log('Request Failed: ' + error);

                        thisVue.success = 'failed';
                        thisVue.statusMessage = 'Not Saved: ' + error;

                        resetSuccess(thisVue, 2000);

                    });
        }
    }

    }


</script>