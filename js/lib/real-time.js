const RestClient = require('./rest');
const Channel = require('./channel');
const AblyChannel = require('./ably/channel');

module.exports = class RealTime {
    constructor({type, endpoint}) {
        if (type !== 'ably') {
            throw new Error('Type not supported');
        }

        this._rest = new RestClient(endpoint);
    }

    static get CHANNEL_EVENTS() {
        return Channel.EVENTS;
    }

    channel(name) {
        return new AblyChannel(name, this._rest);
    }
};
