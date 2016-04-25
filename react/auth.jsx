var Auth = {
    logIn: function (user, remember) {
        remember = typeof remember !== 'undefined' ?  remember : null;
        if (remember) {
            sessionStorage.removeItem('user');
            localStorage.user = JSON.stringify(user);
        } else {
            localStorage.removeItem('user');
            sessionStorage.user = JSON.stringify(user);
        }
    },
    logOut: function (returnKey) {
        returnKey = typeof returnKey !== 'undefined' ?  returnKey : null;
        var user = this.getUser(returnKey);
        localStorage.removeItem('user');
        sessionStorage.removeItem('user');
        return user;
    },
    isAuth: function () {
        try {
            if (localStorage.user) {
                JSON.parse(localStorage.user);
                return true;
            }
            if (sessionStorage.user) {
                JSON.parse(sessionStorage.user);
                return true;
            }
        } catch (e) {
            return false;
        }
        return false;
    },
    getUser: function (key) {
        var data= {};
        try {
            if (localStorage.user) {
                data = JSON.parse(localStorage.user);
            }
            if (sessionStorage.user) {
                data = JSON.parse(sessionStorage.user);
            }
        } catch (e) {
            data = {};
        }
        return data.hasOwnProperty(key) ? data[key] : null;
    }
};

export default Auth;