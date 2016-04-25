import React from 'react'
import {Link} from 'react-router'

var propTypes = {};
var defaultProps = {};

var Friends = React.createClass({
    getInitialState: function () {
        return {users: []}
    },
    getList: function () {
        $.post(Settings.apiUrl + '/profile/friends', {token: Auth.getUser('token')}, function(data) {
            this.setState({users: data.data})
        }.bind(this), "json");
    },
    deleteFriend: function (id) {
        $.post(Settings.apiUrl + '/profile/delete-friend', {token: Auth.getUser('token'), id: id}, function(data) {
            this.getList()
        }.bind(this), "json");
    },
    componentDidMount: function () {
        Settings.setTitle('Friends');
        this.getList();
    },
    renderTable: function () {
        var tbody = this.state.users.map(function (user, key) {
            return <tr key={key}>
                <td>{key+1}</td>
                <td>{user.fname}</td>
                <td>{user.email}</td>
                <td><button onClick={this.deleteFriend.bind(null, user.id)} type="button" className="btn btn-danger">Delete from Friends</button></td>
            </tr>
        }.bind(this));
        return (
            <table className="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>{''}</th>
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

Friends.propTypes = propTypes;
Friends.defaultProps = defaultProps;

export default Friends;