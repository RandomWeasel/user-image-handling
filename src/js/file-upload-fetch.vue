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
            default:'/fetch-file-upload',
            type: String
        },
        fileDest: {
            default: '/temp-file-upload',
            type: String
        },
                'parentIdentity',
                'multiple'
    },

    methods: {
        onFileChange: function(e){
            var thisVue = this;

            //clear any errors
            thisVue.fileErrors = '';

            //get the file
            var filesArray = e.target.files || e.dataTransfer.files;
            var file = filesArray[0];


            //create data to post
            var fileData = new FormData();
            fileData.append('file', file);
            fileData.set('file_dest', thisVue.fileDest);

//            console.log(fileData);

            //upload the file
            fetch(thisVue.postUrl, {
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

            .then(function(response){
//                console.log('file upload response');
//                console.log(response);
                return response.json()

            }).then(function(json){

//                        console.log('file upload json');
//                        console.log(json);

                if(json.success === 'true'){
                    thisVue.fileData = json.fileData;

//                    console.log('the file was uploaded to ' + thisVue.parentIdentity);
                    //emit an event with the fileData
                    bus.$emit(
                        "file-upload", ([thisVue.parentIdentity, thisVue.fileData])
                    );

                    //display the image
                } else {
                    //set the errors data
                    thisVue.fileErrors = json.errors;
                }

            }).catch(function(error){
                console.log('Request Failed: ' + error);

                        var errorMessage = 'Sorry, there was a problem uploading this file - ' + error;

                        thisVue.fileErrors = [];
                        thisVue.fileErrors.file = [errorMessage] ;
            });
        }
    }
    }
</script>