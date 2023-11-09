const api = "";

const query = new URL(location.href).searchParams.get("q");
document.getElementById("query").value = query;
document.getElementById("title").innerText = "Search - " + query;

const start_time = new Date().getTime();

fetch(api + "/search?q=" + query).then(res => res.json().then(res => {
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