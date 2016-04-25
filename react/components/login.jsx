import React from 'react'
import {Link, browserHistory} from 'react-router'

var propTypes = {};
var defaultProps = {};

var Login = React.createClass({
    componentDidMount: function() {
        if (this.props.route.path == '/logout') {
            Pace.restart();
            $.post(Settings.apiUrl + '/user/logout', {token: Auth.logOut('token')}, function() {
            }, "json");
        }
        browserHistory.push('/login');
        Settings.setTitle('Login');
    },
    _submit: function (e) {
        Pace.restart();
        e.preventDefault();
        var rememberMe = this.refs.remember.checked ? 1 : 0;
        var request = $.extend($(e.target).serializeObject(), {rememberMe: rememberMe});
        $.post(Settings.apiUrl + '/user/login', request, function(data) {
            if (data.success) {
                Auth.logIn(data.data, rememberMe);
                browserHistory.push('/')
            } else {
                var errors = data.data.map(function (error) {
                    return error.message;
                });
                toastr.error(errors.join('<br>'), 'Error');
            }
        }.bind(this), "json");
    },
    render: function () {
        return <section className="container">
            <div className="row margin-top-50">
                <div className="col-md-6 col-md-offset-3">
                    <div className="panel panel-primary">
                        <div className="panel-heading">
                            <h3 className="panel-title">Login</h3>
                            <small>
                                Please enter your credentials to login.
                            </small>
                        </div>
                        <div className="panel-body">
                            <form onSubmit={this._submit} noValidate>
                                <div className="form-group">
                                    <label className="control-label" htmlFor="username">Email</label>
                                    <input type="text" placeholder="example@gmail.com" title="Please enter your Email" name="email" className="form-control"/>
                                    <span className="help-block small">Your Email to app</span>
                                </div>
                                <div className="form-group">
                                    <label className="control-label" htmlFor="password">Password</label>
                                    <input type="password" title="Please enter your password" placeholder="******" name="password" className="form-control"/>
                                    <span className="help-block small">Your strong password</span>
                                </div>
                                <div className="row form-inline">
                                    <div className="col-md-6">
                                        <button className="btn btn-success">Login</button>
                                        {' '}
                                        <div className="checkbox">
                                            <label>
                                                <input ref='remember' type="checkbox" defaultChecked={true}/>
                                                Remember Me
                                            </label>
                                        </div>
                                    </div>
                                    <div className="col-md-6 text-right">
                                        <Link to='/register' className="btn btn-primary">Register</Link>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    }
});

Login.propTypes = propTypes;
Login.defaultProps = defaultProps;

export default Login;