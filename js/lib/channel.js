const EventEmitter = require('eventemitter3');

const EVENTS = {
    CONNECTED: 'connected',
    DISCONNECTED: 'disconnected',
    ERROR: 'error',
    MESSAGE: 'message'
};

module.exports = class Channel extends EventEmitter {
    constructor(name, restClient) {
        super();
        this._name = name;
        this._rest = restClient;
        this._token = null;
        this._unloadListener = () => this.destroy();

        window.addEventListener('beforeunload', this._unloadListener, false);
        window.addEventListener('unload', this._unloadListener, false);
    }

    static get EVENTS() {
        return EVENTS;
    }

    async _refreshToken() {
        await this._releaseToken();

        if (!this._name || !this._rest) {
            throw new Error('Channel destroyed');
        }

        this._token = await this._rest.subscribe(this._name);
    }

    async _releaseToken() {
        if (this._token && this._name && this._rest) {
            const token = this._token;
            this._token = null;

            try {
                await this._rest.unsubscribeAsync(this._name, token.id);
            } catch (err) {
                await this._rest.unsubscribe(this._name, token.id).catch(() => {});
            }
        }
    }

    connect() {}

    disconnect() {
        this._releaseToken().catch(console.warn);
    }

    destroy() {
        this.disconnect();
        window.removeEventListener('beforeunload', this._unloadListener);
        window.removeEventListener('unload', this._unloadListener);
        this._name = null;
        this._rest = null;
        this._unloadListener = null;
    }
};
