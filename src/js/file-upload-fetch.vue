<template>

    <div class="user-image">

        <loading :show="showLoading" label="Uploading file..." :overlay-class="'loading-overlay'"></loading>

        <slot name="before-input"></slot>


        <input v-if="multiple" type="file" name="selected_file" @change="onFileChange" multiple="true">

        <input v-else type="file" name="selected_file" @change="onFileChange">


        <slot name="after-input"></slot>

        <template v-if="showPreview">
            <div v-if="fileIsImage" class="preview-uploaded-image" :style="'background-image: url(/' + fileData.path + fileData.filename + ')'">
            </div>

            <div v-if="fileIsDocument" class="preview-uploaded-file">
                <a :href="'/' + fileData.path + fileData.filename" target="_blank">{{fileData.filename}}</a>
            </div>

            <div v-if="fileIsVideo" class="preview-uploaded-file">
                <a :href="'/' + fileData.path + fileData.filename" target="_blank">{{fileData.filename}}</a>
            </div>

            <div v-if="fileIsOther" class="preview-uploaded-file">
                <a :href="'/' + fileData.path + fileData.filename" target="_blank">{{fileData.filename}}</a>
            </div>
        </template>

        <div v-if="success && ! showPreview" class="message-formSuccess">
            Upload Successful
        </div>



        <field-errors :errorObject="fileErrors.file"></field-errors>


    </div>



</template>

<script type="text/ecmascript-6">

    import loading from 'vue-full-loading';

    export default{

        components: {
            loading: loading
        },

        data()
        {
            return {
                fileData: '',
                fileErrors: '',
                success: '',

                //loader
                showLoading: false,
            }
        },

        props: {
            postUrl: {
                type: String,
                default: '/fetch-image-upload'
            },
//        fileDest: {
//            default: '/temp-file-upload',
//            type: String
//        },
//        parentIdentity:{
//            type: Array,
//        },
            multiple: {
                type: Boolean,
                default: false,
            },
            showPreview: {
                type: Boolean,
                default: true,
            },
            uniqueRef: {
                //this unique ref is included in the event the comp emits
                //use to identify a specific instance of the uploader
                type: String,
                required: false,
            }
        },

        mounted: function(){
            console.log(this.postUrl);
        },

        computed: {
            fileIsImage(){

                if(this.fileData.filetype){
                    let fileType = this.fileData.filetype.toLowerCase();

                    let imageTypes = ['jpg', 'jpeg', 'png', 'gif' , 'bmp' , 'jpeg', 'svg'];

                    if(imageTypes.includes(fileType)){
                        return true;
                    }
                }

            },

            fileIsVideo(){
                if(this.fileData.filetype){
                    let fileType = this.fileData.filetype.toLowerCase();

                    let videoTypes = ['mp4','ogx','oga','ogv','ogg','webm','qt'];
                    //TODO pass videoTypes from the MimeTypesService

                    if(videoTypes.includes(fileType)){
                        return true;
                    }
                }

            },

            fileIsDocument(){
                if(this.fileData.filetype){
                    let fileType = this.fileData.filetype.toLowerCase();

                    let documentTypes = ['pdf', 'doc', 'docx', 'pages'];

                    if(documentTypes.includes(fileType)){
                        return true;
                    }
                }
            },

            fileIsOther(){
                if(this.fileData.filetype){
                    if(!this.fileIsImage && !this.fileIsVideo && !this.fileIsDocument){
                        return true;
                    }
                }
            }


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

                this.showLoading = true;

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

                        this.success = true;

                        this.showLoading = false;

//                    console.log('the file was uploaded to ' + this.parentIdentity);
                        //emit an event with the fileData
                        this.$parent.$emit(
                            "file-upload", {
                                uniqueRef: this.uniqueRef,
                                fileData: this.fileData,
                            }
                        );

                        //display the image
                    } else {
                        //set the errors data
                        this.showLoading = false;
                        this.fileErrors = json.errors;
                    }

                }).catch((error) => {
                    console.log('Request Failed: ' + error);

                    this.showLoading = false;

                    var errorMessage = 'Sorry, there was a problem uploading this file - ' + error;

                    this.fileErrors = [];
                    this.fileErrors.file = [errorMessage] ;
                });
            }
        }
    }
</script>