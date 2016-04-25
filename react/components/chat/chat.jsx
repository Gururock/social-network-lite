import React from 'react'
import {Link} from 'react-router'
import ChatBox from './chat-box'

var propTypes = {};
var defaultProps = {};


var msg = [
	{
		user: 1,
		username: 'user 1'
	},
	{
		user: 2,
		username: 'user 2'
	},
	{
		user: 3,
		username: 'user 3'
	}
];


var Chat = React.createClass({
	getInitialState: function () {
		return {
			active: 0
		}	
	},
	componentDidMount: function () {
		var websocket = new WebSocket('ws://snp.aod.local:8080', Auth.getUser('id'));
		websocket.onmessage = function(ev) {
			var message = JSON.parse(ev.data);
			switch (message.type) {
				case 'message':
					this.refs['chat'+message.from].updateHistory();
			}
		}.bind(this)
	},
	closeTab: function (user) {
		
	},
	activateTab: function (user) {
		this.setState({
			active: user
		})
	},
	render: function () {
		var chats = msg.map(function (data, key) {
			return <ChatBox 
				ref={'chat'+data.user}
				user={data.user} 
				username={data.username} 
				onActivate={this.activateTab} 
				key={key} 
				active={data.user == this.state.active} 
				state={1}
				onClose={this.closeTab}
			/>
		}.bind(this));
		return <div className="chat">{chats}</div>
	}
});

Chat.propTypes = propTypes;
Chat.defaultProps = defaultProps;

export default Chat;