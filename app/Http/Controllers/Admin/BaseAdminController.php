<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\RedirectResponse;
use Throwable;

/**
 * All admin CRUD controllers extend this so every write action gets the
 * same crash-proof behavior: run the work, catch anything that throws,
 * log it, and send the admin back with a friendly message instead of a
 * raw error page.
 */
abstract class BaseAdminController extends Controller
{
    /**
     * Run a mutation and turn it into a redirect. $work does the actual
     * database/file work; if it returns a RedirectResponse itself (e.g.
     * redirecting to a newly-created model's own page), that's used as-is
     * with the success message attached — otherwise we fall back to
     * $redirectRoute/$routeParams. On any exception, it's logged and the
     * admin is sent back with their input intact and a generic error —
     * never a stack trace or raw exception message.
     *
     * $successMessage may be a Closure if the message depends on state
     * that only exists after $work() runs (e.g. a model's new status) —
     * passing a plain string would evaluate too early, before the mutation.
     */
    protected function tryAction(Closure $work, string|Closure $successMessage, string $redirectRoute, array $routeParams = []): RedirectResponse
    {
        try {
            $result = $work();
            $message = $successMessage instanceof Closure ? $successMessage() : $successMessage;

            $redirect = $result instanceof RedirectResponse
                ? $result
                : redirect()->route($redirectRoute, $routeParams);

            return $redirect->with('flash_success', $message);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('flash_error', 'Something went wrong while saving. Please try again.');
        }
    }

    /**
     * Same as tryAction, but stays on the current page (back()) on success
     * too — for actions like status toggles or inline updates that don't
     * need a route redirect.
     */
    protected function tryActionBack(Closure $work, string|Closure $successMessage): RedirectResponse
    {
        try {
            $work();
            $message = $successMessage instanceof Closure ? $successMessage() : $successMessage;

            return back()->with('flash_success', $message);
        } catch (Throwable $e) {
            report($e);

            return back()->with('flash_error', 'Something went wrong. Please try again.');
        }
    }
}
