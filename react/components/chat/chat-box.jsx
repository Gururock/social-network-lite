import React from 'react'
import classNames from 'classnames';
import {Link} from 'react-router'
import ChatMessage from './chat-message.jsx'

var propTypes = {};
var defaultProps = {};

var ChatBox = React.createClass({
    getInitialState: function () {
      return {
        minimized: false,
        sent: 0,
        messages: []
      }
    },
    componentDidMount: function () {
        this.updateHistory();
    },
    toggle: function () {
        this.setState({
            minimized: !this.state.minimized
        }, function () {
            if (!this.state.minimized) {
                this.scrollToBottom();
                this.activate()
            }
        })
    },
    scrollToBottom: function () {
        $(this.refs.content).scrollTop($(this.refs.content)[0].scrollHeight);  
    },
    sendMessage: function (event) {
        event.preventDefault();
        var tempMessages = this.state.messages;
        var messages = this.state.messages;
        var message = this.refs.input.value.trim();
        if (message.length) {
            /*tempMessages.push({
                id: 'new' + this.state.sent,
                user: Auth.getUser('token'),
                data: message
            });
            this.setState({
                messages: tempMessages,
                sent: this.state.sent + 1
            }, function () {
                this.refs.input.value = '';
                this.scrollToBottom();
            });*/
            var request = {
                token: Auth.getUser('token'),
                id: this.props.user,
                message: message
            };
            $.post(Settings.apiUrl + '/profile/send-message', request, function(response) {
                if (response.success) {
                    messages.push({
                        id: response.data.id,
                        user: Auth.getUser('token'),
                        data: message
                    });
                    this.setState({
                        messages: messages
                    }, function () {
                        this.refs.input.value = '';
                        this.scrollToBottom();
                    })
                }
            }.bind(this), "json");
        }
    },
    updateHistory: function () {
        var request = {
            token: Auth.getUser('token'),
            id: this.props.user
        };
        $.post(Settings.apiUrl + '/profile/get-history', request, function(response) {
            this.setState({
                messages: response.data
            }, function () {
                this.scrollToBottom();
            })
        }.bind(this), "json");
    },
    activate: function () {
        if (this.refs.input != document.activeElement) {
            this.refs.input.focus();
        }
        this.props.onActivate(this.props.user)
    },
    close: function (event) {
        event.stopPropagation();
        this.props.onClose(this.props.user)
    },
    getMessages: function () {
        return this.state.messages.map(function (msg) {
            return <ChatMessage id={msg.id} key={msg.id} mine={msg.user != this.props.user} data={msg.data} />
        }.bind(this));  
    },
    render: function () {
        var classes = classNames(
            {
                active: this.props.active,
                minimized: this.state.minimized,// && !this.props.hidden,
                'panel-primary': this.props.active && !this.state.minimized,
                'panel-info': !this.props.active && !this.state.minimized,
                'panel-default': this.state.minimized
            },
            [
                'chat-box',
                'panel'
            ]
        );
        return (
            <div className={classes}>
                <div onClick={this.toggle} className="panel-heading">
                    <div className="row">
                        <div className="col-md-10">{this.props.username}</div>
                        <a title="close" onClick={this.close} className="col-md-2">X</a>
                    </div>
                </div>
                <div onClick={this.activate} className="panel-body">
                    <div className="messages" ref='content'>{this.getMessages()}</div>
                    <form onSubmit={this.sendMessage}>
                        <input onFocus={this.activate} ref="input" type="text" placeholder="Type a message..." />
                    </form>
                </div>
            </div>
        )
    }
});

ChatBox.propTypes = propTypes;
ChatBox.defaultProps = defaultProps;

export default ChatBox;