<?php
class ChatStatus
{
	const MISSED = 0;
	const OLD_ENDED = 1;
	const PENDING_AGENT = 2;
	const ACTIVE=3;
	const BROWSE = 5;
	const NOT_STARTED = 17;
	const ENDED_BY_AGENT = 16;
	const ENDED_BY_CLIENT = 15;
	const ENDED_DUE_AGENT_INACTIVITY=14;
	const ENDED_DUE_CLIENT_INACTIVITY=13;
	const ENDED_DUE_BLOCK = 18;
}

class ChatState
{
	const ACTIVE = 0;
	const MINIMIZED = 1;
}

class UserTypes
{
	const AGENT=1;
	const CLIENT=2;
	const SYSTEM=0;
}

class ActionTypes
{
	const NEW_MESSAGE=0;
	const CHANGE_STATUS=1;
	const START_QUEUE = 2;
}

class WebHookTypes
{
	const AGENT_LOGIN = 0;
	const NEW_VISITOR = 3;
	const CHAT_REQUEST = 4;
	const AGENT_ACCEPT = 5;
	const SETTINGS_CHANGED = 6;
}