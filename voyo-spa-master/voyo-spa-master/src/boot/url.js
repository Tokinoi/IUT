export function queryString(params) {
	return Object.keys(params).map(key => key + '=' + encodeURIComponent(params[key])).join('&')
}

export function currentURL() {
	return window.location.pathname
}