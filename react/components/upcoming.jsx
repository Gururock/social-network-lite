import React from 'react'
import {Link} from 'react-router'

var propTypes = {};
var defaultProps = {};

var Upcoming = React.createClass({
    getInitialState: function () {
        return {users: []}  
    },
    getList: function () {
        $.post(Settings.apiUrl + '/profile/upcoming-birthdays', {token: Auth.getUser('token')}, function(data) {
            this.setState({users: data.data})
        }.bind(this), "json");
    },
    componentDidMount: function () {
        Settings.setTitle('Upcoming Birthdays');
        this.getList();
    },
    renderTable: function () {
        var tbody = this.state.users.map(function (user, key) {
            return <tr key={key}>
                <td>{key+1}</td>
                <td>{user.fname}</td>
                <td>{user.email}</td>
                <td>{user.birthday}</td>
            </tr>
        }.bind(this));
        return (
            <table className="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Birthday</th>
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

Upcoming.propTypes = propTypes;
Upcoming.defaultProps = defaultProps;

export default Upcoming;