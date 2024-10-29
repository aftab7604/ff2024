var PRINT_CONTENT_DIR = "rtl";

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

const visibleTextarea = document.getElementById("answer");
const hiddenTextarea = document.getElementById("content");

visibleTextarea.addEventListener('input', function(){
    hiddenTextarea.value = visibleTextarea.value;
});

$(document).on("change", "#dir-select", function () {
    let ThreadDIV = $("#chat-thread");
    let selectedValue = $(this).val();

    if (selectedValue == 1)
    {
        ThreadDIV.css("direction", "rtl");
        PRINT_CONTENT_DIR = "rtl";
    }
    else if (selectedValue == 2)
    {
        ThreadDIV.css("direction", "ltr");
        PRINT_CONTENT_DIR = "ltr";
    }
    else
    {
        alert("Something went wrong with Direction change.");
    }
});