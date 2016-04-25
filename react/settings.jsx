import {browserHistory} from 'react-router'

var Settings = {
    baseUrl: '',
    apiUrl: 'http://snp.aod.local',
    setTitle: function (title) {
        document.title = "SNP - " + title;
    },
    setAjaxListener: function () {
        $(document).ajaxError(function(e, xhr, settings, exception) {
            switch (xhr.status) {
                case 500:
                    toastr.error(xhr.statusText, 'Error '+ xhr.status);
                    break;
                default:
                    browserHistory.push('/login');
            }
        })
    }
};

export default Settings;