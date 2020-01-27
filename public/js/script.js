const updateBts = document.querySelectorAll('.update-name');

console.log(updateBts);

document.addEventListener('click', function (event) {
	// If the clicked element doesn't have the right selector, bail
	if (!event.target.matches('.update-name')) return;
	// Log the clicked element in the console
    event.target.parentNode.style.display = "none";
    event.target.parentNode.nextElementSibling.style.display = "block";
}, false);
