<template>

    <div>
        <slot name="before-input"></slot>


        <input v-if="multiple" type="file" name="selected_file" @change="onFileChange" multiple="true">

        <input v-else type="file" name="selected_file" @change="onFileChange">


        <slot name="after-input"></slot>

        <field-errors :errorObject="fileErrors.file"></field-errors>


    </div>



</template>

<script type="text/ecmascript-6">
    export default{
        data()
    {
        return {
            fileData: '',
            fileErrors: '',
        }
    }
    ,

    props: {
        postUrl: {
            type: String,
            default: '/fetch-file-upload'
        },
//        fileDest: {
//            default: '/temp-file-upload',
//            type: String
//        },
                parentIdentity: Array,
                multiple: Boolean
    },

    mounted: function(){
        console.log(this.postUrl);
    },

    methods: {
        onFileChange(e) {

            //clear any errors
            this.fileErrors = '';

            //get the file
            var filesArray = e.target.files || e.dataTransfer.files;
            var file = filesArray[0];


            //create data to post
            var fileData = new FormData();
            fileData.append('file', file);
//            fileData.set('file_dest', this.fileDest);

//            console.log(fileData);

            console.log(this.postUrl);

            //upload the file
            fetch(this.postUrl, {
                method: 'POST',
                headers: {
//                    "Content-Type": "multipart/form-data",
                    "X-Request-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.head.querySelector('meta[name="csrf-token"]').content,
//                    "Accept": "multipart/form-data"
                },
                credentials: 'same-origin',
                body: fileData
            }).then(utilities.promiseStatus)

            .then((response) => {
//                console.log('file upload response');
//                console.log(response);
                return response.json()

            }).then((json) => {

//                        console.log('file upload json');
//                        console.log(json);

                if(json.success === 'true'){
                    this.fileData = json.fileData;

//                    console.log('the file was uploaded to ' + this.parentIdentity);
                    //emit an event with the fileData
                    bus.$emit(
                        "file-upload", ([this.parentIdentity, this.fileData])
                    );

                    //display the image
                } else {
                    //set the errors data
                    this.fileErrors = json.errors;
                }

            }).catch((error) => {
                console.log('Request Failed: ' + error);

                        var errorMessage = 'Sorry, there was a problem uploading this file - ' + error;

                        this.fileErrors = [];
                        this.fileErrors.file = [errorMessage] ;
            });
        }
    }
    }
</script>