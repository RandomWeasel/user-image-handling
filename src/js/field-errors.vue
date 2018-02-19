<template>

    <div class="message-formError" v-if="errorObject">

        <span v-for="error in errorObject">
            {{ error }} <br>
        </span>
    </div>

</template>


<script>

    export default{

        props: [
                'errorObject'
        ],

        mounted: function(){
            console.log(this.$parent);
        }

    }


</script>