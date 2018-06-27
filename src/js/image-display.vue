<template>

    <div v-bind:class="{isPrimary : imageData.is_primary, notShown : ! imageData.is_shown}" class="wrap-image-imageDisplay">

        <div class="image" :style="'background-image: url(' + imageData.path + '/' + versionedImage + ')' "></div>

        <div class="block-text">

            <slot name="info-panel-default">
               <span v-if="imageData.is_primary" class="isPrimary">
                <span v-html="iconIsPrimary"></span>
                Main Image
            </span>

            <span v-if="! imageData.is_shown" class="notShown">
                <span v-html="iconIsHidden"></span>
                Not Displayed
            </span>

                <span v-else></span>

            <span v-if="categories" class="category">
                {{categoryName}}
            </span>

                <span class="caption">{{imageData.caption}}</span>
            </slot>


            <slot name="info-panel-extra"></slot>
        </div>

        <slot></slot>

    </div>



</template>


<script>

    //import icons
    import iconIsPrimary from './icons/icon-main-image.js';
    import iconIsHidden from './icons/icon-hidden.js';


    export default {
       props: [
            'image',
           'categories',
        ],

        data(){
            return {
                imageData: Object.assign({}, this.image),
                versionedImage: this.image.filename, //initial value - no version number
                'iconIsPrimary': iconIsPrimary,
                'iconIsHidden': iconIsHidden
            }
        },

        mounted: function(){

            var thisVue = this;

            //listener for updated image data
            bus.$on('updatedImageData', function(updatedImageData){


                //if the image was set to be the primary image, remove this from all displayed images
                //this will be re-added to the currently edited image next
                if(updatedImageData.is_primary == true){
                    thisVue.imageData.is_primary = false;
                }

                //update the displayed image
                //but only if it's the same image
                if(updatedImageData.id == thisVue.imageData.id){
                    thisVue.imageData = Object.assign({}, updatedImageData);

                    //create a new versioned image to force a reload
                    thisVue.versionedImage = thisVue.makeVersionedImage();
                }
            })
        },

        computed: {
            categoryName: function(){
                var categoryId = this.imageData.category_id;

                if( this.categories && categoryId ){

                    var category = this.categories.find(category => category.id == categoryId);
                    var categoryName = category.name;
                    return categoryName

                }  else {
                    return '';
                }
            }
        },

        methods: {
            makeVersionedImage: function(){
                var timestamp = Math.random();
                console.log('new versioned image image-display');
                return this.imageData.filename + '?v=' + timestamp;
            }
        }
    }
</script>