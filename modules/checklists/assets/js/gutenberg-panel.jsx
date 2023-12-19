const { registerPlugin } = wp.plugins;
const { PluginSidebarMoreMenuItem, PluginSidebar } = wp.editPost;
const { Fragment, Component } = wp.element;
const { __ } = wp.i18n;
const { hooks } = wp;

import CheckListIcon from './CheckListIcon.jsx';

class PPChecklistsPanel extends Component {
    isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            showRequiredLegend: false,
            requirements: [],
            failedRequirements: [],
        };
    }

    componentDidMount() {
        // Bind the arrow function to access 'this' inside subscribe
        const boundPerformChecksBeforePostUpdate = this.performChecksBeforePostUpdate.bind(this);
    
        // Subscribe to the data
        this.unsubscribe = wp.data.subscribe(boundPerformChecksBeforePostUpdate);
    
        this.isMounted = true;
        this.updateRequirements(ppChecklists.requirements);

        hooks.addAction('pp-checklists.update-failed-requirements', 'publishpress/checklists', this.updateFailedRequirements.bind(this), 10);
        hooks.addAction('pp-checklists.requirements-updated', 'publishpress/checklists', this.handleRequirementStatusChange.bind(this), 10);
    }

    componentWillUnmount() {
        if (this.unsubscribe) {
            this.unsubscribe();
        }
    
        hooks.removeAction('pp-checklists.update-failed-requirements', 'publishpress/checklists');
        hooks.removeAction('pp-checklists.requirements-updated', 'publishpress/checklists');

        this.isMounted = false;
    }

    /**
     * Hook to failed requirement to update block requirements.
     * 
     * @param {Array} failedRequirements 
     */
    updateFailedRequirements(failedRequirements) {
        if (this.isMounted) {
            this.setState({ failedRequirements: failedRequirements });
        }
    };

    /**
     * Handle requirement status change
     */
    handleRequirementStatusChange = () => {
        this.updateRequirements(this.state.requirements);
    };

    /**
     * Update sidebar requirements
     * 
     * @param {Array} Requirements 
     */
    updateRequirements = (Requirements) => {
        if (this.isMounted) {
            const showRequiredLegend = Object.values(Requirements).some((req) => req.rule === 'block');

            const updatedRequirements = Object.entries(Requirements).map(([key, req]) => {
                const id = req.id || key;
                const element = document.querySelector(`#ppch_item_${id}`);

                if (element) {
                    req.status = element.value == 'yes' ? true : false;
                }
                req.id = id;

                return req;
            });

            this.setState({ showRequiredLegend, requirements: updatedRequirements });
        }
    };

    /**
     * Add a method to perform checks before updating
     */
    performChecksBeforePostUpdate = () => {
        var editor = wp.data.dispatch('core/editor');
        var notices = wp.data.dispatch('core/notices');
        var savePost = editor.savePost;

        /**
         * Our less problematic solution till gutenberg Add a way 
         * for third parties to perform additional save validation 
         * in https://github.com/WordPress/gutenberg/issues/13413
         * is this issue as it also solves third party conflict with
         * locking post (Rankmath, Yoast SEO etc)
         */
        editor.savePost = function () {
            notices.removeNotices('publishpress-checklists-validation');

            if (typeof this.state.failedRequirements.block === "undefined" || this.state.failedRequirements.block.length === 0) {
                savePost();
            } else {
                wp.data.dispatch('core/edit-post').closePublishSidebar();
                notices.createErrorNotice(__("Please complete the required(*) checklists task.", "publishpress-checklists"), {
                    id: 'publishpress-checklists-validation',
                    isDismissible: true
                });
                wp.data.dispatch('core/edit-post').openGeneralSidebar('publishpress-checklists-panel/checklists-sidebar');
            }
        }.bind(this);
    };

    render() {
        const { showRequiredLegend, requirements } = this.state;

        return (
            <Fragment>
                <PluginSidebarMoreMenuItem
                    target="checklists-sidebar"
                    icon={<CheckListIcon />}
                >
                    {__("Checklists", "publishpress-checklists")}
                </PluginSidebarMoreMenuItem>
                <PluginSidebar
                    name="checklists-sidebar"
                    title={__("Checklists", "publishpress-checklists")}
                >
                    <div id="pp-checklists-sidebar-content" className="components-panel__body is-opened">
                        <ul id="pp-checklists-sidebar-req-box">
                            {requirements.length === 0 ? (
                                <p>
                                    <em>
                                        {ppChecklists.empty_checklist_message}
                                    </em>
                                </p>
                            ) : (
                                requirements.map((req, key) => (
                                    <li
                                        key={`pp-checklists-req-panel-${key}`}
                                        className={`pp-checklists-req panel-req pp-checklists-${req.rule} status-${req.status ? 'yes' : 'no'} ${req.is_custom ? 'pp-checklists-custom-item' : ''
                                            }`}
                                        data-id={key}
                                        data-type={req.type}
                                        data-extra={req.extra || ''}
                                        onClick={() => {
                                            if (req.is_custom) {
                                                const element = document.querySelector(`#pp-checklists-req-${req.id}` + ' .status-label');
                                                if (element) {
                                                    element.click();
                                                }
                                            }
                                        }}
                                    >
                                        {req.is_custom ? (
                                            <input type="hidden" name={`_PPCH_custom_item[${req.id}]`} value={req.status ? 'yes' : 'no'} />
                                        ) : null}
                                        <div className={`status-icon dashicons ${req.is_custom ? (req.status ? 'dashicons-yes' : '') : (req.status ? 'dashicons-yes' : 'dashicons-no')}`}></div>
                                        <div className="status-label">
                                            {req.label}
                                            {req.rule === 'block' ? (
                                                <span className="required">*</span>
                                            ) : null}
                                        </div>
                                    </li>
                                ))
                            )}
                        </ul>
                        {showRequiredLegend ? (
                            <em>
                                (*) {__("required", "publishpress-checklists")}
                            </em>
                        ) : null}
                    </div>
                </PluginSidebar>
            </Fragment>
        );
    }
}

const ChecklistsTitle = () => (
    <div className="pp-checklists-toolbar-icon">
        Checklists {/* Don't translate, the text is been used in CSS */}
    </div>
);

registerPlugin("publishpress-checklists-panel", {
    render: PPChecklistsPanel,
    icon: <ChecklistsTitle />,
});
