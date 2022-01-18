var socket;
var clientId;
var sessionId;
var socketTimeout;
var connectionTriesCount;

jQuery(function () {
    connectionTriesCount = 0;
    jQuery("body").on('mcu-setup-socket', function (e) {
        wplc_setupSocket();
    });

    jQuery("body").unbind('mcu-send-message');
    jQuery("body").on('mcu-send-message', function (e, data) {
        wplc_sendMessage(data.sessionID, clientId, data.message);
    });

    jQuery("body").unbind('mcu-send-file');
    jQuery("body").on('mcu-send-file', function (e, data) {
        wplc_sendFile(data.sessionID, clientId, data.url, data.size, data.name);
    });

    jQuery("body").unbind('mcu-chat-close');
    jQuery("body").on('mcu-chat-close', function (e, data) {
        wplc_closeChat(data.sessionID, clientId, data.statusID)
    });

    jQuery("body").unbind('mcu-chat-block-visitor');
    jQuery("body").on('mcu-chat-block-visitor', function (e, data) {
        wplc_blockIP(data.sessionID, clientId, data.statusID)
    });

    jQuery("body").on('mcu-send-typing', function (e, data) {
        wplc_sendTyping(data.sessionID, clientId, 'TYPING');
    });

    jQuery("body").on('mcu-socket-join', function (e, data) {
        wplc_joinChat(data.sessionID, clientId)
        sessionId = data.sessionID;
    });

    jQuery("body").on('mcu-socket-attend', function (e, data) {
        wplc_attendChat(data.sessionID, clientId)
        sessionId = data.sessionID;
    });


    jQuery("body").on('mcu-close-socket', function (e, data) {
        wplc_closeConnection()
    });


});

function wplc_setupSocket() {
    if (socketTimeout !== null && typeof socketTimeout !== 'undefined') {
        clearTimeout(socketTimeout);
        socketTimeout = null;
    }
    jQuery("body").trigger('mcu-socket-connecting');
    if (!window.WebSocket) {
        //If the user's browser does not support WebSockets, give an alert message
        alert("Your browser does not support the WebSocket API!");
    } else {
        if (socket instanceof WebSocket && socket.readyState === socket.OPEN) {
            socket.close();
        }
        socket = new WebSocket(localization_data.socket_url);
        wplc_socketEventsSetup();
    }
}

function wplc_socketEventsSetup() {
    socket.onopen = function () {
        wplc_login();
    }

    /*socket.onerror = function (error) {
        if (connectionTriesCount < 5 && (socketTimeout === null || typeof socketTimeout === 'undefined')) {
            socketTimeout = setTimeout(wplc_setupSocket, 5000);
            connectionTriesCount++;
        } else if (connectionTriesCount >= 5 && localization_data.wplc_is_chat_page) {
            wplc_force_reload_mcu_data();
        }
    }*/

    socket.onclose = function (event) {
        if (connectionTriesCount < 5 && event.code != 1000 && (socketTimeout === null || typeof socketTimeout === 'undefined')) {
            socketTimeout = setTimeout(wplc_setupSocket, 5000);
            connectionTriesCount++;
        } else if (connectionTriesCount >= 5) {
            wplc_force_reload_mcu_data();
        }
    };

    socket.onmessage = function (socketPackage) {
        var data = JSON.parse(socketPackage.data);

        if (data.hasOwnProperty('action')) {
            /*if (data.action == 'login') {
                clientId = data.key;
                sessionId = data.sid;
            } else */
            if (data.action == 'chat') {
                if (data.notification == 'text') {
                    if (data.hasOwnProperty('from') && data.from !== localization_data.user_id
                        && data.hasOwnProperty('sid')
                    ) {
                        var messageToRender = {
                            msg: data.message,
                            is_file: false,
                            added_at: wplc_convertTicksToDate(data.tick),
                            originates: data.senderType === "Client" ? 2 : 1,
                            id: data.id,
                            sessionId: data.sid
                        }
                        jQuery("body").trigger('mcu-new-message', messageToRender);
                    }
                } else if (data.notification == 'file') {
                    if (data.hasOwnProperty('from') && data.from !== localization_data.user_id
                        && data.hasOwnProperty('sid')
                    ) {
                        var messageToRender = {
                            msg: data.url,
                            is_file: true,
                            added_at: wplc_convertTicksToDate(data.tick),
                            originates: 2,
                            id: data.id,
                            sessionId: data.sid
                        }
                        jQuery("body").trigger('mcu-new-message', messageToRender);
                    }
                }
            } else if (data.action == 'indication') {
                if (data.notification == 'end') {
                    if (data.hasOwnProperty('sid')) {
                        jQuery("body").trigger('mcu-chat-ended', data.sid);
                    }
                } else if (data.notification == 'typing') {
                    if (data.hasOwnProperty('sid')) {
                        var typingChatElement = ".chat_list.wplc_p_cul[data-sid='" + data.sid + "'] #wplc_guest_typing";
                        jQuery(typingChatElement).show();
                        setTimeout(function () {
                            jQuery(typingChatElement).hide();
                        }, 2000);
                    }
                } else if (data.notification == 'CompleteChat') {
                    jQuery("body").trigger('mcu-chat-list-remove', data.data);
                } else if (data.notification == 'NewChat') {
                    jQuery("body").trigger('mcu-chat-list-add', data.data);
                } else if (data.notification == 'UpdateChat') {
                    jQuery("body").trigger('mcu-chat-list-update', data.data);
                } else if (data.notification == 'agent_login') {
                    sessionStorage.removeItem('AgentLoginRequest');
                    jQuery("body").trigger('mcu-socket-connected')
                    clientId = data.clientKey;
                    sessionStorage.setItem('AgentID', clientId);
                    if (data.hasOwnProperty('sessions')
                    ) {
                        data.sessions.forEach(function (session) {
                            jQuery("body").trigger('mcu-chat-list-add', session);
                        })

                    }
                } else if (data.notification == 'InvalidLogin') {
                    wplc_force_reload_mcu_data();
                } else if (data.notification == 'AgentAlreadyJoined') {
                    alert("Another agent already joined this chat session.")
                }
            }
        }
    };
}

function wplc_login() {

    var clientKey = sessionStorage.getItem('AgentID');

    var loginRequest = {
        action: "request",
        notification: "agent_login",
        id: IdGenerator.getNext(),
        tick: wplc_convertDateToTicks(new Date()),
        pid: localization_data.portal_id,
        aid: localization_data.user_id,
        name: localization_data.agent_name,
        department: localization_data.agent_department,
        cmSessionId: localization_data.chat_server_session,
        from: typeof (clientKey) !== 'undefined' && clientKey !== 'null' && clientKey !== null ? clientKey : ''
    }


    socket.send(JSON.stringify(loginRequest));
    sessionStorage.setItem('AgentLoginRequest', new Date());
    setTimeout(function(){
        var loginRequestTime = sessionStorage.getItem('AgentLoginRequest');
        if(loginRequestTime!==null && loginRequestTime!==undefined)
        {
            wplc_force_reload_mcu_data();
        }
    },3000)


    // console.log("login agent",loginRequest);
}

function wplc_joinChat(sessionID, agentID) {
    //check to ensure that the socket variable is present i.e. the browser support tests passed
    if (socket) {
        var messageToSend = {
            action: 'request',
            notification: 'agent_join',
            id: IdGenerator.getNext(),
            sid: sessionID,
            pid: localization_data.portal_id,
            tick: wplc_convertDateToTicks(new Date()),
            from: agentID,
            agentName: localization_data.agent_name,
            agentEmail: localization_data.agent_email
        };

        socket.send(JSON.stringify(messageToSend));
    }
}

function wplc_attendChat(sessionID, agentID) {
    //check to ensure that the socket variable is present i.e. the browser support tests passed
    if (socket) {
        var messageToSend = {
            action: 'request',
            notification: 'agent_attend',
            id: IdGenerator.getNext(),
            sid: sessionID,
            pid: localization_data.portal_id,
            tick: wplc_convertDateToTicks(new Date()),
            from: agentID,
            agentName: localization_data.agent_name,
            agentEmail: localization_data.agent_email
        };

        socket.send(JSON.stringify(messageToSend));
    }
}

function wplc_closeChat(sessionID, agentID, statusID) {
    //check to ensure that the socket variable is present i.e. the browser support tests passed
    if (socket) {
        const endMessage = {
            action: 'indication',
            notification: 'end',
            id: IdGenerator.getNext(),
            tick: wplc_convertDateToTicks(new Date()),
            from: agentID,
            sid: sessionID,
            status: statusID,
            pid: localization_data.portal_id,
        };
        socket.send(JSON.stringify(endMessage));
    }
}

function wplc_blockIP(sessionID, agentID, statusID) {
    if (socket) {
        const endMessage = {
            action: 'indication',
            notification: 'block',
            id: IdGenerator.getNext(),
            tick: wplc_convertDateToTicks(new Date()),
            from: agentID,
            sid: sessionID,
            status: statusID,
            pid: localization_data.portal_id,
        };
        socket.send(JSON.stringify(endMessage));
    }
}

function wplc_sendTyping(sessionID, agentID) {
    if (socket) {
        const typing = {
            action: 'indication',
            notification: 'typing',
            id: IdGenerator.getNext(),
            pid: localization_data.portal_id,
            tick: wplc_convertDateToTicks(new Date()),
            from: agentID,
            sid: sessionID
        };
        socket.send(JSON.stringify(typing));
    }
}

function wplc_sendMessage(sessionID, agentID, message) {
    //check to ensure that the socket variable is present i.e. the browser support tests passed
    if (socket) {
        //get the message text input element
        if (message !== "") {
            var messageToSend = {
                action: 'request',
                notification: 'chat',
                id: IdGenerator.getNext(),
                message: message,
                tick: wplc_convertDateToTicks(new Date()),
                from: agentID,
                sid: sessionID,
                pid: localization_data.portal_id
            };

            socket.send(JSON.stringify(messageToSend));
        } else {
            alert("You must enter a message to be sent!");
        }
    }
}

function wplc_sendFile(sessionID, agentID, url, size, name) {
    if (socket) {
        if (url !== "") {
            var messageToSend = {
                action: 'request',
                notification: 'file',
                id: IdGenerator.getNext(),
                url: url,
                name: name,
                size: size,
                tick: wplc_convertDateToTicks(new Date()),
                from: agentID,
                sid: sessionID,
                pid: localization_data.portal_id
            };

            socket.send(JSON.stringify(messageToSend));
        } else {
            alert("You must select a file to be sent!");
        }
    }
}

function wplc_closeConnection() {
    if (socket && socket.readyState === socket.OPEN) {
        socket.close();
    }
    clearTimeout(socketTimeout);
}

function wplc_force_reload_mcu_data() {
    if(localization_data.wplc_is_chat_page) {
        var url = window.location.href;
        if (url.indexOf('?') > -1) {
            url += '&wplc_action=invalid_login'
        } else {
            url += '?wplc_action=invalid_login'
        }
        window.location.href = url;
    }
}

var IdGenerator = (function () {
    var currentID = -1;

    function Init() {
        currentID = 100000;
    }

    return {
        getNext: function () {
            if (currentID < 0) {
                Init();
            }
            currentID++;
            return currentID;
        }
    };
})();