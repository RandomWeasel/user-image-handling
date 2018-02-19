<template>

    <div v-bind:class="{isPrimary : imageData.is_primary, notShown : ! imageData.is_shown}" class="wrap-image-imageDisplay">

        <div class="image" :style="'background-image: url(/img/property-images/' + imageData.filename + ')' "></div>

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
                imageData: Object.assign({}, this.image)
            }
        },

        mounted: function(){

            var thisVue = this;

            //listener for updated image data
            bus.$on('updatedImageData', function(updatedImageData){
                thisVue.imageData = Object.assign({}, updatedImageData);
            })
        },

        computed: {
            categoryName: function(){
                if( this.categories ){
                    var categoryId = this.imageData.category_id;
//                    console.log(categoryId);
                    var category = this.categories.find(category => category.id == categoryId);
                    var categoryName = category.name;
                    console.log('category: ' + category);
                    console.log(categoryName);
                    return categoryName
                }  else {
                    return '';
                }
            }
        }
    }
</script>