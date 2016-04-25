import React from 'react'
import classNames from 'classnames';

var propTypes = {};
var defaultProps = {};

var ChatMessage = React.createClass({
	render: function () {
		return <div id={this.props.id}>
			<div className={classNames('message', this.props.mine ? 'right' : 'left')}>{this.props.data}</div>
			<div className="clearfix"></div>
		</div>
	}
});

ChatMessage.propTypes = propTypes;
ChatMessage.defaultProps = defaultProps;

export default ChatMessage;