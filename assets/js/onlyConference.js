import {enterMeeting, initWebsocket, leaveMeeting} from "./websocket";
import {close, inIframe, initModeratorIframe} from "./moderatorIframe";
import {initStarSend} from "./endModal";
import {initStartWhiteboard} from "./startWhiteboard";
import * as mdb from 'mdb-ui-kit'; // lib
import ClipboardJS from 'clipboard'
import {initStartIframe} from "./createConference";
var frameId;
var api = new JitsiMeetExternalAPI(domain, options);
var joined = false;
api.addListener('chatUpdated', function (e) {
    if (e.isOpen == true) {
        document.querySelector('#logo_image').classList.add('transparent');
    } else {
        document.querySelector('#logo_image').classList.remove('transparent');
    }

});


api.addListener('videoConferenceJoined', function (e) {
    enterMeeting();
    initStartWhiteboard();
    joined = true;
    window.onbeforeunload = function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return closeTabText;
    }
    api.addListener('videoConferenceLeft', function (e) {
        leaveMeeting();
        initStarSend();
        api = null;
    });

    if (setTileview === 1) {
        api.executeCommand('setTileView', {enabled: true});
    }
    if (avatarUrl !== '') {
        api.executeCommand('avatarUrl', avatarUrl);
    }
    if (setParticipantsPane === 1) {
        api.executeCommand('toggleParticipantsPane', {enabled: true});
    }
})

var iframe = document.querySelector('#jitsiConferenceFrame0');
iframe.style.height = '100%';

window.addEventListener('message', function (e) {
    // add here more commands up to now only close is defined.
    const data = e.data;
    const decoded = JSON.parse(data);
    if (decoded.type === 'pleaseClose') {
        if (api) {
            api.executeCommand('hangup')
        }else {
            close(frameId);
        }
    } else if (decoded.type === 'init') {
        frameId = decoded.frameId;
    }
});


window.onmessage = function (event) {
    if (event.data === "jitsi-closed") {
        window.onbeforeunload = null;
        window.close();
    }
};

function docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

function checkClose() {

    if (!api || !joined){
        leaveMeeting();
        initStarSend();
    }
    if (api){
        api.executeCommand('hangup');
    }
    leaveMeeting();
}

docReady(function () {
    var clipboard = new ClipboardJS('.copyLink');
    initModeratorIframe(checkClose);
    initWebsocket(websocketTopics);
    initStartIframe();
});
