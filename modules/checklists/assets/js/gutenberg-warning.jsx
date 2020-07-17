const {Component} = React;
const {__} = wp.i18n;
const {Fragment} = wp.element;
const {PluginPrePublishPanel} = wp.editPost;
const {registerPlugin} = wp.plugins;
const {hooks} = wp;

String.prototype.stripTags = function () {
    return this.replace(/(<([^>]+)>)/ig, "");
};

class PPChecklistsWarning extends Component {
    isMounted = false;

    componentDidMount() {
        this.isMounted = true;
    }

    componentWillUnmount() {
        this.isMounted = false;
    }

    constructor() {
        super();

        this.state = {
            requirements: {}
        };

        this.updateFailedRequirements = this.updateFailedRequirements.bind(this);

        hooks.addAction('pp-checklists.update-failed-requirements', 'publishpress/checklists', this.updateFailedRequirements, 10);
    };

    updateFailedRequirements(failedRequirements) {
        if (this.isMounted) {
            this.setState({requirements: failedRequirements});
        }
    };

    render() {
        if (typeof this.state.requirements.block === "undefined" ||
            (this.state.requirements.block.length === 0 && this.state.requirements.warning.length === 0)) {
            return (null);
        }

        let messageBlock = (null);
        if (this.state.requirements.block.length > 0) {
            messageBlock = (<div>
                <p>{__('Please complete the following tasks before publishing:', 'publishpress-checklists')}</p>
                <ul>
                    {this.state.requirements.block.map(
                        (item, i) => <li key={i}>
                            <span className="dashicons dashicons-no"></span><span>{item.stripTags()}</span></li>
                    )}
                </ul>
            </div>);
        }

        let messageWarning = (null);
        if (this.state.requirements.warning.length > 0) {
            let message = this.state.requirements.block.length > 0 ?
                __('Not required, but important:', 'publishpress-checklists') : __('Are you sure you want to publish anyway?', 'publishpress-checklists');

            messageWarning = (<div>
                <p>{message}</p>
                <ul>
                    {this.state.requirements.warning.map(
                        (item, i) => <li key={i}>
                            <span className="dashicons dashicons-no"></span><span>{item.stripTags()}</span>
                        </li>
                    )}
                </ul>
            </div>);
        }

        return (<Fragment>
            <PluginPrePublishPanel
                name="publishpress-checklists-pre-publishing-panel"
                title={__('Checklist')}
                initialOpen="true"
            >
                <div className="pp-checklists-failed-requirements-warning">
                    {messageBlock}
                    {messageWarning}
                </div>
            </PluginPrePublishPanel>
        </Fragment>);
    }
}

registerPlugin('pp-checklists-warning', {
    icon: 'warning',
    render: () => (<PPChecklistsWarning/>)
});
