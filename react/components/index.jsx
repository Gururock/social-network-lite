import React from 'react'
import {Link} from 'react-router'

var propTypes = {};
var defaultProps = {};

var Index = React.createClass({
    componentDidMount: function () {
        Settings.setTitle('Dashboard');
    },
    render: function () {
        return <div>
            Hello world
        </div>
    }
});

Index.propTypes = propTypes;
Index.defaultProps = defaultProps;

export default Index;