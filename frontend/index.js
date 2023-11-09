document.getElementById("search").addEventListener('submit', event => {
	event.preventDefault();

	let query = document.getElementById("query").value.trim();
	if (query == "") {
		alert("Please input a search query!");
	} else {
		location.href = "/search.html?q=" + query;
	}
});