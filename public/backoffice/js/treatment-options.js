function handleSelectChange(role)
{
    console.log('works');

    var select = document.getElementById("choosePatient");
    var selectedValue = select.value;

    if (selectedValue)
    {
        var route = "/" + role + "/dashboard" + "/treatment/patient/" + selectedValue;

        window.location.href = route;
    }else{
        var route = "/" + role;

        window.location.href = route;
    }
}

$(document).on("change", "#dir-select", function () {
    let ContentDIV = $("#div-content");
    let selectedValue = $(this).val();

    if (selectedValue == 1)
    {
        ContentDIV.css("direction", "rtl");
    }
    else if (selectedValue == 2)
    {
        ContentDIV.css("direction", "ltr");
    }
    else
    {
        alert("Something went wrong with Direction change.");
    }
});