<template>

    <div class="wrap-buttonsRotate">

        <span class="button-rotate --acw" v-on:click="rotateAcw"></span>
        <span class="button-rotate --cw" v-on:click="rotateCw"></span>

    </div>

</template>


<script>

    export default{
        data(){
            return {
                'rotation': 0
            }
        },

    props: [
           'imageId'
    ],

    methods: {
        rotateAcw: function(){
            var thisVue = this;

            thisVue.rotation = thisVue.rotation - 90;

            //reset to 0 if fully rotated
            if(thisVue.rotation == - 360){
                thisVue.rotation = 0;
            }

            console.log(thisVue.rotation);

            bus.$emit(
                    "rotation", [thisVue.imageId, thisVue.rotation]
            );
        },

        rotateCw: function(){
            var thisVue = this;

            thisVue.rotation = thisVue.rotation + 90;

            //reset to 0 if fully rotated
            if(thisVue.rotation == 360){
                thisVue.rotation = 0;
            }

            console.log(thisVue.rotation);

            bus.$emit(
                    "rotation", [thisVue.imageId, thisVue.rotation]
            );
        }
    }



    }

</script>