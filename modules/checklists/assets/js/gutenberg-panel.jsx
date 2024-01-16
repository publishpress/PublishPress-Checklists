const { registerPlugin } = wp.plugins;
const { PluginSidebarMoreMenuItem, PluginSidebar } = wp.editPost;
const { Fragment, Component } = wp.element;
const { __ } = wp.i18n;
const { hooks } = wp;

import CheckListIcon from './CheckListIcon.jsx';

class PPChecklistsPanel extends Component {
    isMounted = false;
    oldStatus = '';
    currentStatus = '';

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
        const boundperformPostStatusCheck = this.performPostStatusCheck.bind(this);
    
        // Subscribe to the data
        this.unsubscribe = wp.data.subscribe(boundperformPostStatusCheck);

        this.isMounted = true;
        if (typeof ppChecklists !== "undefined") {
            this.updateRequirements(ppChecklists.requirements);
        }

        hooks.addAction('pp-checklists.update-failed-requirements', 'publishpress/checklists', this.updateFailedRequirements.bind(this), 10);
        hooks.addAction('pp-checklists.requirements-updated', 'publishpress/checklists', this.handleRequirementStatusChange.bind(this), 10);

        /**
         * Our less problematic solution till gutenberg Add a way 
         * for third parties to perform additional save validation 
         * in this issue https://github.com/WordPress/gutenberg/issues/13413
         * is this solution as it also solves third party conflict with
         * locking post (Rankmath, Yoast SEO etc)
         */
        let coreEditor   = wp.data.dispatch('core/editor');
        let notices  = wp.data.dispatch('core/notices');
        let coreSavePost = coreEditor.savePost;

        wp.data.dispatch('core/editor').savePost = async (options) => {
            options = options || {};

            let publishing_post = false;
                
            if (options.isAutosave || options.isPreview) {
                publishing_post = false
            } else if (this.currentStatus !== '') {
                publishing_post = (this.currentStatus !== 'publish') ? false : true;
            } else {
                if (!wp.data.select('core/edit-post').isPublishSidebarOpened() && wp.data.select('core/editor').getEditedPostAttribute('status') !== 'publish' && wp.data.select('core/editor').getCurrentPost()['status'] !== 'publish') {
                    publishing_post = false;
                } else if (wp.data.select('core/edit-post').isPublishSidebarOpened() && wp.data.select('core/editor').getEditedPostAttribute('status') == 'publish') {
                    publishing_post = true;
                } else if (!wp.data.select('core/edit-post').isPublishSidebarOpened() && wp.data.select('core/editor').getEditedPostAttribute('status') == 'publish') {
                    publishing_post = true;
                }
            }

            if (!publishing_post || typeof this.state.failedRequirements.block === "undefined" || this.state.failedRequirements.block.length === 0) {
                return coreSavePost(options);
            } else {
                wp.data.dispatch('core/edit-post').closePublishSidebar();
                notices.createErrorNotice(__("Please complete the required(*) checklists task.", "publishpress-checklists"), {
                    id: 'publishpress-checklists-validation',
                    isDismissible: true
                });
                wp.data.dispatch('core/edit-post').openGeneralSidebar('publishpress-checklists-panel/checklists-sidebar');

                /**
                 * change status to draft or old status if failed to 
                 * solve further save draft button not working. This is
                 * because at this state, the status has been updated to publish 
                 * and further click on "Save draft" from editor UI won't work
                 * as that doesn't update the status to publish
                 */
                if (this.oldStatus !== '') {
                    wp.data.dispatch('core/editor').editPost({status: this.oldStatus, pp_checklists_post_status_edit: true});
                }
                return;
            }
        };
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
     *  This is the best way to get edited post status. 
     * For now, both getEditedPostAttribute('status') and 
     * getCurrentPost()['status'] are not helpful because they don't usually return same
     * status or valid status between when a post Publish button is used / Save draft is clicked
     * for new and already published post.
    */
    performPostStatusCheck = () => {

        let coreEdiPost  = wp.data.dispatch('core/editor').editPost;

        if (!this.oldStatus || this.oldStatus == '') {
            this.oldStatus = wp.data.select('core/editor').getCurrentPost()['status'];
        }
        
        wp.data.dispatch('core/editor').editPost = async (edits, options) => {
            options = options || {};
            if (options.pp_checklists_edit_filtered === 1 || options.pp_checklists_post_status_edit === 1) {
                return coreEdiPost(edits, options);
            }
            
            if (typeof edits === 'object' && edits.status) {
                // set status to be used later when preventing publish for posts that doesn't meet requirement.
                this.currentStatus = edits.status;
            }
            options.pp_checklists_edit_filtered = 1;
            return coreEdiPost(edits, options);
        };
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
                                        {__("You don't have to complete any Checklist tasks.", "publishpress-checklists")}
                                    </em>
                                </p>
                            ) : (
                                requirements.map((req, key) => (
                                    <li
                                        key={`pp-checklists-req-panel-${key}`}
                                        className={`pp-checklists-req panel-req pp-checklists-${req.rule} status-${req.status ? 'yes' : 'no'} ${req.is_custom ? 'pp-checklists-custom-item' : ''
                                            }`}
                                        data-id={req.id}
                                        data-type={req.type}
                                        data-extra={req.extra || ''}
                                        data-source={req.source || ''}
                                        onClick={() => {
                                            if (req.is_custom) {
                                                const element = document.querySelector(`#pp-checklists-req-${req.id}` + ' .status-label');
                                                if (element) {
                                                    element.click();
                                                }
                                            }
                                        }}
                                    >
                                        {req.is_custom || req.require_button ? (
                                            <input type="hidden" name={`_PPCH_custom_item[${req.id}]`} value={req.status ? 'yes' : 'no'} />
                                        ) : null}
                                        <div className={`status-icon dashicons ${req.is_custom ? (req.status ? 'dashicons-yes' : '') : (req.status ? 'dashicons-yes' : 'dashicons-no')}`}></div>
                                        <div className="status-label">
                                            {req.label}
                                            {req.rule === 'block' ? (
                                                <span className="required">*</span>
                                            ) : null}
                                            {req.require_button ? (
                                                <div className="requirement-button-task-wrap">
                                                    <button type="button" className="button button-secondary pp-checklists-check-item">
                                                        {__("Check Now", "publishpress-checklists")}
                                                        <span className="spinner"></span>
                                                    </button>
                                                    <div className="request-response"></div>
                                                </div>
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
