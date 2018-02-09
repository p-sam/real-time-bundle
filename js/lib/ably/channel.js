const ably = require('ably');
const Channel = require('../channel');

function authCallbackFactory(ablyChannel) {
    return (_, cb) => {
        ablyChannel._refreshToken()
            .then(() => cb(null, ablyChannel._token.payload.token))
            .catch(err => cb(err));
    };
}

module.exports = class AblyChannel extends Channel {
    constructor(name, restClient) {
        super(name);
        this._rest = restClient;
        this._ablyRealtime = null;
    }

    connect() {
        if (!this._ablyRealtime) {
            this._ablyRealtime = new ably.Realtime({
                authCallback: authCallbackFactory(this)
            });

            this._ablyRealtime.connection.on('connected', () => {
                this._ablyRealtime.channels.get(this._name).subscribe(message => {
                    if (message.name === this._name) {
                        this.emit(Channel.EVENTS.MESSAGE, message.data);
                    }
                });
                this.emit(Channel.EVENTS.CONNECTED);
            });

            this._ablyRealtime.connection.on('disconnected', () => this.emit(Channel.EVENTS.DISCONNECTED));
        }
    }

    disconnect() {
        if (this._ablyRealtime) {
            this._ablyRealtime.close();
            this._ablyRealtime = null;
            this.emit(Channel.EVENTS.DISCONNECTED);
        }
        super.disconnect();
    }
};
