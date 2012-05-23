var elements = document.getElementsByClassName("btn btn-mini btn-danger");

for(var i=0; i<elements.length; i++) {
    //elements[i].onclick = showConfirm;
	elements[i].onclick=function(){return confirm("Are you sure you wish to delete document?\nCannot be recovered!")};
}

function showConfirm()
{
	return confirm('Are you sure you wish to delete document? Cannot be recovered!');
}