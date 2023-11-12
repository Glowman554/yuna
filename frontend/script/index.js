document.getElementById("search_form").addEventListener('submit', event => {
	event.preventDefault();

	let query = document.getElementById("search").value.trim();
	if (query == "") {
		alert("Please input a search query!");
	} else {
		location.href = "/search.html?q=" + query;
	}
});