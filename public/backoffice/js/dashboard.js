function handleSelectChange(role)
{
    var select = document.getElementById("choosePatient");
    var selectedValue = select.value;

    if (selectedValue)
    {
        var route = "/" + role + "/patient/" + selectedValue + "/documents";

        window.location.href = route;
    }else{
        var route = "/" + role;

        window.location.href = route;
    }
}

function handleCopyToClipboard()
{
    var textarea = document.getElementById("answer");
    
    textarea.select();
    textarea.setSelectionRange(0, 99999);
    
    document.execCommand("copy");
    
    alert("Text copied!");
}

function handleEditButton()
{
    var textarea = document.getElementById("answer");

    textarea.removeAttribute("readonly");
    textarea.focus();
}

function handleNameEdit(event, id)
{
    event.preventDefault();

    let nameE = document.getElementById("name_" + id);
    let inputE = document.getElementById("input_" + id);
    let linkE = document.getElementById("link_" + id);
    let iconE = document.getElementById("icon_" + id);

    if (inputE.style.display == "none")
    {
        nameE.style.display = "none";
        inputE.style.display = "inline-block";
        linkE.classList.remove('text-primary');
        linkE.classList.add('text-success');
        iconE.setAttribute("data-feather", "check-square");
        feather.replace();

        inputE.focus();
        const length = inputE.value.length;
        inputE.setSelectionRange(length, length);
    }
    else
    {
        nameE.style.display = "inline-block";
        inputE.style.display = "none";
        linkE.classList.remove('text-success');
        linkE.classList.add('text-primary');
        iconE.setAttribute("data-feather", "edit");
        feather.replace();

        updateFileName(id, inputE, nameE);
    }
}

function updateFileName(id, inputE, nameE)
{
    let pName = nameE.innerHTML;
    nameE.innerHTML = 'Changing Name...';

    if (pName != inputE.value)
    {
        $.ajax({
            url: '/user/dashboard/document/'+ id +'/update-name',
            type: 'PUT',
            data: {
                file_name: inputE.value
            },
            success: function (response) {
                if(response.success)
                {
                    nameE.innerHTML = inputE.value;
                }
                else
                {
                    nameE.innerHTML = pName;
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    }
    else
    {
        nameE.innerHTML = pName;
    }
}