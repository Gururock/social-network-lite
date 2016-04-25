import React from 'react'
import {Link} from 'react-router'

var propTypes = {};
var defaultProps = {};

var Potential = React.createClass({
    getInitialState: function () {
        return {users: []}  
    },
    getList: function () {
        $.post(Settings.apiUrl + '/profile/potential', {token: Auth.getUser('token')}, function(data) {
            this.setState({users: data.data})
        }.bind(this), "json");
    },
    componentDidMount: function () {
        Settings.setTitle('Potential Friends');
        this.getList();
    },
    renderTable: function () {
        var tbody = this.state.users.map(function (user, key) {
            return <tr key={key}>
                <td>{key+1}</td>
                <td>{user.fname}</td>
                <td>{user.email}</td>
            </tr>
        }.bind(this));
        return (
            <table className="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                    {tbody}
                </tbody>
            </table>
        )
    },
    render: function () {
        return <div>{this.renderTable()}</div>
    }
});

Potential.propTypes = propTypes;
Potential.defaultProps = defaultProps;

export default Potential;