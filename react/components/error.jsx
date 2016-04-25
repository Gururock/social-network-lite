import React from 'react'
import {Link} from 'react-router'

var propTypes = {};
var defaultProps = {};

var Error = React.createClass({
    getInitialState: function () {
        var error = {};
        switch (this.props.route.path) {
            default:
                error.code = 404;
                error.title = 'Page Not Found';
                error.message = 'Sorry, but the page you are looking for has not been found. Try checking the URL for error, then hit the refresh button on your browser or try found something else in our app.';
        }
        return error 
    },
    componentDidMount: function () {
        Settings.setTitle(this.state.title);  
    },
    render: function () {
        return <section className="container">
            <div className="row margin-top-50">
                <div className="col-md-6 col-md-offset-3">
                    <div className="panel panel-danger">
                        <div className="panel-heading">
                            <h3 className="panel-title">{this.state.code}</h3>
                            <small>
                                {this.state.title}
                            </small>
                        </div>
                        <div className="panel-body">
                            {this.state.message}
                        </div>
                    </div>
                    <div>
                        <Link to='/' className="btn btn-accent">Back to app</Link>
                    </div>
                </div>
            </div>
        </section>
        
    }
});

Error.propTypes = propTypes;
Error.defaultProps = defaultProps;

export default Error;