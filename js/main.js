let profilePicFileURL;

document.addEventListener("DOMContentLoaded", (event) => {

    //[1] Step 1
    let submitBtn = document.getElementById("submitBtn");
    submitBtn.addEventListener("click", uploadProfilePic);

    //[2] Step 2
    let profilePicFile = document.getElementById("profilePicFile");
    profilePicFile.addEventListener("change", (event) => { 
        
        profilePicFileURL = event.target.files[0];

        let imgPreview = document.getElementById("imgPreview");

       let reader = new FileReader();
       reader.onload = function (e) {
            console.log("Generating Image Preview");
            imgPreview.src = e.target.result
        }

        reader.readAsDataURL(event.target.files[0]);
    });

});

const uploadProfilePic = (event) => {

    let httpRequest = new XMLHttpRequest();
    let url = "php/handler.php";

    let formData = new FormData();
    formData.append("actionType", "uploadFile");
    formData.append("profilePicFile", profilePicFileURL);

    httpRequest.open("POST", url, true);

    //Send the proper header information along with the request
    //httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    //Call a function when the state changes.
    httpRequest.onreadystatechange = function() {
        if(httpRequest.readyState == 4 && httpRequest.status == 200) {
            console.log("Server Response: " + httpRequest.responseText);
            //document.getElementById('output').innerHTML = httpRequest.responseText;
        }
    }

    //error due to network issues
    httpRequest.onerror = function(e) {
        console.log("error");
    };	

    httpRequest.ontimeout = function (e) {
        console.log("error, timeout");
    };	

    httpRequest.send(formData);
}