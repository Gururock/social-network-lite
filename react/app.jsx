import React from 'react'
import ReactDom from 'react-dom'
import { Navigation, State, Router, IndexRoute, Route, Link, browserHistory, Redirect } from 'react-router'
import Settings from './settings'
import Auth from './auth'

//globals
global.Settings = Settings;
global.Auth = Auth;

Settings.setAjaxListener();

//components
import Index from './components/index'
import Friends from './components/friends'
import Potential from './components/potential'
import Upcoming from './components/upcoming'
import Birthdays from './components/birthdays'
import Login from './components/login'
import Register from './components/register'
import Error from './components/error'



var App = React.createClass({
    componentWillMount: function () {
        if (!Auth.isAuth()) {
            browserHistory.push('/login');
            return false;
        }
    },
    render: function () {
        return ( 
            <div className='container'>
                <div className="row margin-top-50">
                    <div className="col-md-12">
                        <div className="panel panel-primary">
                            <div className="panel-heading">
                                <div className="row">
                                    <div className="col-md-3 text-left">
                                        Welcome {Auth.getUser('fname')}
                                    </div>
                                    <div className="col-md-6 text-center">
                                        <Link className="btn btn-success" to='/friends'>All friends</Link>
                                        {' '}
                                        <Link className="btn btn-success" to='/potential-friends'>Potential friends</Link>
                                        {' '}
                                        <Link className="btn btn-success" to='/birthdays'>Birthdays</Link>
                                        {' '}
                                        <Link className="btn btn-success" to='/upcoming-birthdays'>Upcoming birthdays</Link>
                                    </div>
                                    <div className="col-md-3 text-right">
                                        <Link className="btn btn-warning" to='/logout'>LogOut</Link>
                                    </div>
                                </div>
                            </div>
                            <div className="panel-body">
                                {this.props.children}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
});

ReactDom.render((
    <Router history={browserHistory}>
        <Route path='/' component={App}>
            <IndexRoute component={Index}/>
            <Route path="/friends" component={Friends}/>
            <Route path="/potential-friends" component={Potential}/>
            <Route path="/birthdays" component={Birthdays}/>
            <Route path="/upcoming-birthdays" component={Upcoming}/>
        </Route>
        <Route path="/not-found" component={Error}/>
        <Route path="/logout" component={Login}/>
        <Route path="/login" component={Login}/>
        <Route path="/register" component={Register}/>
        <Redirect from="*" to="/not-found" />
    </Router>
), document.getElementById('app'));