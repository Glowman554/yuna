const results_per_page = 100;

const url = new URL(location.href).searchParams;

const page = parseInt(url.get("page") || "0");
const query = url.get("q");

document.getElementById("query").value = query;
document.getElementById("title").innerText = "Search - " + query;
document.getElementById("page").innerText = page;

const start_time = new Date().getTime();

fetch("/api/search?q=" + query + "&offset=" + results_per_page * page + "&limit=" + results_per_page).then(res => res.json().then(res => {
	if (res.error) {
		console.log(res);
		alert("Fetch failed! " + res.error);
		return;
	}

	document.getElementById("results").innerText = res.length + " search results";
	const result_div = document.getElementById("result");

	for (const i of res) {
		const div = document.createElement("div");

		const link = document.createElement("a");
		link.setAttribute("href", i.link);
		link.innerText = i.title;

		div.appendChild(link);

		result_div.appendChild(div);
	}

	const time = new Date().getTime() - start_time;
	document.getElementById("results_time").innerText = "Search took " + (time / 1000) + "s";
}));

document.getElementById("prev").addEventListener('click', event => {
	location.href = "/search.html?q=" + query + "&page=" + (page - 1);
});

document.getElementById("next").addEventListener('click', event => {
	location.href = "/search.html?q=" + query + "&page=" + (page + 1);
});


if (page == 0) {
	document.getElementById("prev").disabled = true;
}