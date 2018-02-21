<template>

    <div v-bind:class="{isPrimary : imageData.is_primary, notShown : ! imageData.is_shown}" class="wrap-image-imageDisplay">

        <div class="image" :style="'background-image: url(/img/property-images/' + versionedImage + ')' "></div>

        <div class="block-text">

            <span v-if="imageData.is_primary" class="isPrimary">Main Image</span>

            <span v-if="! imageData.is_shown" class="notShown">Not Displayed</span>

            <span v-else></span>

            <span v-if="categories" class="category">
                {{categoryName}}
            </span>

            <span class="caption">{{imageData.caption}}</span>
        </div>

        <slot></slot>

    </div>



</template>


<script>
    export default {
       props: [
            'image',
           'categories'
        ],

        data(){
            return {
                imageData: Object.assign({}, this.image),
                versionedImage: this.image.filename
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

                    thisVue.$set(thisVue, 'versionedImage', thisVue.imgPath());
                    thisVue.versionedImage = thisVue.imgPath();
                }
            })
        },

        computed: {
            categoryName: function(){
                var categoryId = this.imageData.category_id;

                if( this.categories && categoryId ){

                    var category = this.categories.find(category => category.id == categoryId);
                    var categoryName = category.name;
                    console.log('category: ' + category);
                    console.log(categoryName);
                    return categoryName

                }  else {
                    return '';
                }
            }
        },

        methods: {
            imgPath: function(){
                var timestamp = Math.random();
                console.log(timestamp);
                return this.imageData.filename + '?v=' + timestamp;
            }
        }
    }
</script>