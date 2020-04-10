<?php

namespace Corals\Modules\Directory\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Directory\DataTables\ListingsDataTable;
use Corals\Modules\Directory\Http\Requests\ListingRequest;
use Corals\Foundation\Http\Requests\BulkRequest;
use Corals\Modules\Directory\Models\Listing;
use Corals\Modules\Utility\Classes\Schedule\ScheduleManager;
use Corals\Modules\Utility\Models\Rating\Rating;
use Corals\Modules\Utility\Traits\Gallery\ModelHasGallery;
use Corals\Modules\CMS\Traits\SEOTools;
use Corals\Modules\Utility\Facades\Category\Category;


class ListingsController extends BaseController
{
    use ModelHasGallery, SEOTools;

    protected $excludedRequestParams = ['categories', 'options', 'schedule'];

    public $view_prefix = '';

    public function __construct()
    {
        $this->resource_url = config('directory.models.listing.resource_url');

        $this->title = 'Directory::module.listing.title';
        $this->title_singular = 'Directory::module.listing.title_singular';

        $this->setVariables();

        parent::__construct();
    }

    public function setVariables()
    {
        $this->resource_url = config('directory.models.listing.resource_url');
        $this->view_prefix = 'Directory::listings';
    }

    /**
     * @param ListingRequest $request
     * @param ListingsDataTable $dataTable
     * @return mixed
     */
    public function index(ListingRequest $request, ListingsDataTable $dataTable)
    {
        return $dataTable->render('Directory::listings.index');
    }

    /**
     * @param ListingRequest $request
     * @return $this
     */
    public function create(ListingRequest $request)
    {

        $listing = new Listing();

        $scheduleManager = new ScheduleManager($listing);

        $directory_schedules = $scheduleManager->getSchedule();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view($this->view_prefix . '.create_edit')->with(compact('listing', 'directory_schedules'));
    }


    /**
     * @param ListingRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(ListingRequest $request)
    {
        try {
            $data = $request->except($this->excludedRequestParams);

            if (user()->cannot('admin', Listing::class)) {
                $data['user_id'] = user()->id;
            } else {
                $data['user_id'] = $request->get('user_id') ? $request->get('user_id') : null;
            }

            $listing = Listing::query()->create($data);

            $categories = $request->get('categories', []);

            $listing->categories()->sync($categories);

            $listing->indexRecord();

            Category::setModelOptions($request, $listing);

            $schedule = $request->get('schedule');

            $scheduleManager = new ScheduleManager($listing);

            $scheduleManager->createSchedule($schedule);

            event('notifications.directory.listing_created', ['user' => user(), 'listing' => $listing]);

            if ($listing->status == "pending" && !user()->can('admin', Listing::class)) {
                flash(trans('Corals::messages.success.submitted', ['item' => $this->title_singular]))->success();
            } else {
                flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
            }

        } catch (\Exception $exception) {
            log_exception($exception, Listing::class, 'store');
        }

        return redirectTo($listing->getEditUrl($this->resource_url));

    }


    /**
     * @param ListingRequest $request
     * @param Listing $listing
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ListingRequest $request, Listing $listing)
    {
        $scheduleManager = new ScheduleManager($listing);

        $directory_schedules = $scheduleManager->getSchedule();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $listing->name])]);

        return view($this->view_prefix . '.create_edit')->with(compact('listing', 'directory_schedules'));
    }

    public function update(ListingRequest $request, Listing $listing)
    {
        try {
            $data = $request->except($this->excludedRequestParams);

            if (user()->can('admin', Listing::class)) {
                $data['user_id'] = $request->get('user_id') ? $request->get('user_id') : null;
            }

            $current_listing_status = $listing->status;

            $listing->update($data);

            $categories = $request->get('categories', []);

            $listing->categories()->sync($categories);

            $listing->indexRecord();

            Category::setModelOptions($request, $listing);

            $schedule = $request->get('schedule');

            $scheduleManager = new ScheduleManager($listing);

            $scheduleManager->updateSchedule($schedule);
            if (($current_listing_status == "pending") && ($current_listing_status != $listing->status)) {
                if (user()->can('admin', Listing::class)) {
                    if (!empty($listing->user_id) && $listing->status == 'active') {
                        event('notifications.directory.listing_approved', ['user' => $listing->user, 'listing' => $listing]);
                    } elseif (!empty($listing->user_id) && $listing->status == 'inactive') {
                        event('notifications.directory.listing_rejected', ['user' => $listing->user, 'listing' => $listing]);
                    }
                }
            }

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Listing::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    public function bulkAction(BulkRequest $request)
    {
        try {

            $action = $request->input('action');
            $selection = json_decode($request->input('selection'), true);

            switch ($action) {
                case 'delete':
                    foreach ($selection as $selection_id) {
                        $listing = Listing::findByHash($selection_id);
                        $listing_request = new ListingRequest;
                        $listing_request->setMethod('DELETE');
                        $this->destroy($listing_request, $listing);
                    }
                    $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
                    break;

                case 'active' :
                    foreach ($selection as $selection_id) {
                        $listing = Listing::findByHash($selection_id);
                        if (user()->can('Directory::listing.update')) {
                            $listing->update([
                                'status' => 'active'
                            ]);
                            $listing->save();
                            $message = ['level' => 'success', 'message' => trans('Directory::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Directory::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;

                case 'inActive' :
                    foreach ($selection as $selection_id) {
                        $listing = Listing::findByHash($selection_id);
                        if (user()->can('Directory::listing.update')) {
                            $listing->update([
                                'status' => 'inactive'
                            ]);
                            $listing->save();
                            $message = ['level' => 'success', 'message' => trans('Directory::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Directory::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
                case 'pending' :
                    foreach ($selection as $selection_id) {
                        $listing = Listing::findByHash($selection_id);
                        if (user()->can('Directory::listing.update')) {
                            $listing->update([
                                'status' => 'pending'
                            ]);
                            $listing->save();
                            $message = ['level' => 'success', 'message' => trans('Directory::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Directory::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
                case 'archived' :
                    foreach ($selection as $selection_id) {
                        $listing = Listing::findByHash($selection_id);
                        if (user()->can('Directory::listing.update') ) {
                            $listing->update([
                                'status' => 'archived'
                            ]);
                            $listing->save();
                            $message = ['level' => 'success', 'message' => trans('Directory::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Directory::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
            }


        } catch (\Exception $exception) {
            log_exception($exception, Listing::class, 'bulkAction');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }


    public function destroy(ListingRequest $request, Listing $listing)
    {
        try {
            $listing->clearMediaCollection($listing->galleryMediaCollection);
            $listing->delete();
            $listing->schedules()->delete();
            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular]), 'class' => $listing->slug];
        } catch (\Exception $exception) {
            log_exception($exception, Listing::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }


        return response()->json($message);
    }
}
