async function restCall(baseUrl, method, route) {
    const url = new URL(route, baseUrl).toString();

    if (method === '_BEACON') {
        navigator.sendBeacon(url);
        return undefined;
    }

    const response = await fetch(url, {method, credentials: 'same-origin'});

    if (response.status === 204) {
        return undefined;
    }

    if (response.status === 200) {
        return response.json();
    }

    throw new Error(`got status ${response.status} ${response.statusText} on ${url}`);
}

module.exports = class RestClient {
    constructor(baseUrl) {
        this._baseUrl = baseUrl;
    }

    async subscribe(channel) {
        return restCall(this._baseUrl, 'POST', 'presence/' + channel);
    }

    async unsubscribe(channel, uuid) {
        return restCall(this._baseUrl, 'DELETE', `presence/${channel}/${uuid}`);
    }

    async unsubscribeAsync(channel, uuid) {
        return restCall(this._baseUrl, '_BEACON', `presence/${channel}/${uuid}/beacon_unsubscribe`);
    }
};
