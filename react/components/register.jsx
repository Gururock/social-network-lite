import React from 'react'
import {Link, browserHistory} from 'react-router'

var propTypes = {};
var defaultProps = {};

var Register = React.createClass({
    getInitialState: function() {
      return {
          hobbies: []
      }
    },
    componentWillMount: function () {
        if (Auth.isAuth()) {
            browserHistory.push('/')
        }
    },
    componentDidMount: function() {
        Pace.restart();
        Settings.setTitle('Register');
        $.get(Settings.apiUrl + '/site/hobbies', function(data) {
            this.setState({hobbies: data})
        }.bind(this), "json");
    },
    _submit: function (e) {
        e.preventDefault();
        if (this.refs.pass1.value != this.refs.pass2.value) {
            toastr.error('Passwords does not match', 'Error');
            return;
        }
        Pace.restart();
        $.post(Settings.apiUrl + '/user/register', $(e.target).serialize(), function(data) {
            if (data.success) {
                browserHistory.push('/')
            } else {
                var errors = data.data.map(function (error) {
                    return error.message;
                });
                toastr.error(errors.join('<br>'), 'Error');
            }
        }.bind(this), "json");
    },
    renderHobbies: function() {
        var hobbies = this.state.hobbies.map(function(hobby, key) {
          return (
              <div key={key} className="checkbox">
                  <label>
                      <input type="checkbox" name='hobbies[]' value={hobby.id} />
                      {hobby.title}
                  </label>
              </div>
          )
        });
        return <div>{hobbies}</div> 
    },
    render: function () {
        return <section className="container">
            <div className="row margin-top-50">
                <div className="col-md-6 col-md-offset-3">
                    <div className="panel panel-primary">
                        <div className="panel-heading">
                            <h3 className="panel-title">Register</h3>
                            <small>
                                Please enter your credentials to register.
                            </small>
                        </div>
                        <div className="panel-body">
                            <form onSubmit={this._submit} noValidate>
                                <div className="form-group">
                                    <label className="control-label" htmlFor="username">Email</label>
                                    <input type="text" placeholder="example@gmail.com" title="Please enter your Email" name="email" className="form-control"/>
                                </div>
                                <div className="form-group">
                                    <label className="control-label" htmlFor="password">Password</label>
                                    <input type="password" ref='pass1' title="Please enter your password" placeholder="******" className="form-control"/>
                                    <span className="help-block small">Your strong password</span>
                                </div>
                                <div className="form-group">
                                    <label className="control-label" htmlFor="password">Confirm Password</label>
                                    <input type="password" ref='pass2' title="Please enter your password again" placeholder="******" name="password" className="form-control"/>
                                    <span className="help-block small">Confirm Your strong password</span>
                                </div>
                                <div className="form-group">
                                    <label className="control-label" htmlFor="username">Full name</label>
                                    <input type="text" placeholder="John Doe" title="Please enter your Full name" name="fname" className="form-control"/>
                                </div>
                                <div className="form-group">
                                    <label className="control-label" htmlFor="username">Birth date</label>
                                    <input type="date" title="Please enter your Birth date" name="birth" className="form-control"/>
                                    <span className="help-block small">example: 08-05-1993</span>
                                </div>
                                <div className="form-group">
                                    <label className="control-label" htmlFor="username">Hobbies</label>
                                </div>
                                {this.renderHobbies()}
                                <div>
                                    <button className="btn btn-success pull-left">Register</button>
                                    <Link to='/login' className="btn btn-primary pull-right">Back to Login</Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    }
});

Register.propTypes = propTypes;
Register.defaultProps = defaultProps;

export default Register;