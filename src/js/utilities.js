

//PREVENT AUTOCOMPLETE
// Targets fields with the class .prevent-autocomplete which should initially be set to readonly.
// When the input field is clicked, the readonly attribute is removed
// this prevents the browser from autofilling these fields but allows the user to edit them
function preventAutocomplete(){
    $('.prevent-autocomplete').on('focus', function(){
        $(this).attr('readonly', false);
    });
}



//check that a successful response was returned from a promise
//status 422 is returned by laravel when there are validation errors - so will still need to resolve the promise in order to display the errors
function promiseStatus(response) {
    if (response.status >= 200 && response.status < 300  || response.status == 422) {
        return Promise.resolve(response)
    } else {
        console.log(response);
        return Promise.reject(new Error(response.statusText + " [" + response.status + "]" ))
    }
}


//reset the 'success' message on a vue instance
function resetSuccess(vue, time){
    setTimeout(function(){
        vue.success = '';
        vue.statusMessage = '';
    }, time)
}

//take a v-model and convert it to a JS date
function toDate(item){
    if(item == null || item == ''){
        return '';
    } else {
        return new Date(item);
    }

}


export default {promiseStatus, resetSuccess, toDate}