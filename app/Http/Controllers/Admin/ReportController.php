<?php

namespace App\Http\Controllers\Admin;

use App\CommissionLog;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\UserLogin;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction()
    {
        $page_title = 'Transaction Logs';
        $transactions = Transaction::with('user')->orderBy('id','desc')->paginate(getPaginate());
        $empty_message = 'No transactions.';
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }

    public function transactionSearch(Request $request)
    {
        $request->validate(['search' => 'required']);
        $search = $request->search;
        $page_title = 'Transactions Search - ' . $search;
        $empty_message = 'No transactions.';

        $transactions = Transaction::with('user')->whereHas('user', function ($user) use ($search) {
            $user->where('username', 'like',"%$search%");
        })->orWhere('trx', $search)->orderBy('id','desc')->paginate(getPaginate());

        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }

    public function loginHistory(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $page_title = 'User Login History Search - ' . $search;
            $empty_message = 'No search result found.';
            $login_logs = UserLogin::whereHas('user', function ($query) use ($search) {
                $query->where('username', $search);
            })->orderBy('id','desc')->paginate(getPaginate());
            return view('admin.reports.logins', compact('page_title', 'empty_message', 'search', 'login_logs'));
        }
        $page_title = 'User Login History';
        $empty_message = 'No users login found.';
        $login_logs = UserLogin::orderBy('id','desc')->paginate(getPaginate());
        return view('admin.reports.logins', compact('page_title', 'empty_message', 'login_logs'));
    }

    public function loginIpHistory($ip)
    {
        $page_title = 'Login By - ' . $ip;
        $login_logs = UserLogin::where('user_ip',$ip)->orderBy('id','desc')->paginate(getPaginate());
        $empty_message = 'No users login found.';
        return view('admin.reports.logins', compact('page_title', 'empty_message', 'login_logs'));

    }

    public function commissions()
    {
        $page_title = 'Commission Log';
        $logs       = CommissionLog::where('type','deposit_commission')->with(['user','bywho'])->latest()->paginate(getPaginate());
        $emptyMessage = 'No commission log found';
        return view('admin.reports.commission-log', compact('page_title', 'logs', 'emptyMessage'));
    }

    public function commissionSearch(Request $request)
    {
        $request->validate(['search' => 'required']);
        $search = $request->search;
        $page_title = 'Commission Log Search -'.$search;
        $logs = CommissionLog::whereHas('user', function ($user) use ($search) {
                $user->where('username', 'like',"%$search%");
            })->orWhere('trx', $search)->with(['user','bywho'])->latest()->paginate(getPaginate());
        $emptyMessage = 'No log.';
        return view('admin.reports.commission-log', compact('page_title', 'logs', 'emptyMessage','search'));
    }

}
