<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\TopUpRequest;
use App\Models\BalanceTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopUpController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = TopUpRequest::with(['user', 'processor']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $requests = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $stats = [
            'pending' => TopUpRequest::where('status', 'pending')->count(),
            'approved' => TopUpRequest::where('status', 'approved')->count(),
            'rejected' => TopUpRequest::where('status', 'rejected')->count(),
        ];
        
        // Get global QR code from first user with topup_qr_code (superadmin sets it)
        $globalQrCode = User::whereNotNull('topup_qr_code')->first();
        
        return view('superadmin.topup.index', compact('requests', 'stats', 'status', 'globalQrCode'));
    }

    public function approve(TopUpRequest $topUpRequest)
    {
        if ($topUpRequest->status !== 'pending') {
            return back()->with('error', 'Request sudah diproses.');
        }

        DB::beginTransaction();
        try {
            // Update request status
            $topUpRequest->update([
                'status' => 'approved',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Add balance to user
            $user = $topUpRequest->user;
            $balanceBefore = $user->balance;
            $user->balance += $topUpRequest->amount;
            $user->save();

            // Record transaction
            BalanceTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $topUpRequest->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
                'notes' => 'Top-up saldo via ' . ($topUpRequest->payment_method ?? 'Manual'),
            ]);

            DB::commit();
            return back()->with('success', 'Top-up disetujui. Saldo user berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, TopUpRequest $topUpRequest)
    {
        if ($topUpRequest->status !== 'pending') {
            return back()->with('error', 'Request sudah diproses.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $topUpRequest->update([
            'status' => 'rejected',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Top-up ditolak.');
    }

    public function uploadGlobalQrCode(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Get any existing global QR code first
        $existingQr = User::whereNotNull('topup_qr_code')->first();

        if ($request->hasFile('qr_code')) {
            // Delete old QR code if exists
            if ($existingQr && $existingQr->topup_qr_code) {
                // Use Storage facade to delete from public disk
                \Storage::disk('public')->delete($existingQr->topup_qr_code);
            }

            // Store new QR code - explicitly use 'public' disk
            $qrCode = $request->file('qr_code');
            $qrCodeName = 'global_qr_' . time() . '.' . $qrCode->extension();
            $qrCode->storeAs('qr_codes', $qrCodeName, 'public');

            // Update or create user record with QR code
            // We'll store it on a special system user or the first user record
            if ($existingQr) {
                $existingQr->update(['topup_qr_code' => 'qr_codes/' . $qrCodeName]);
            } else {
                // Create a dummy user record or update first user
                $user = User::first();
                if ($user) {
                    $user->update(['topup_qr_code' => 'qr_codes/' . $qrCodeName]);
                }
            }

            return back()->with('success', 'QR Code berhasil diupload.');
        }

        return back()->with('error', 'Gagal upload QR Code.');
    }

    public function deleteGlobalQrCode()
    {
        $existingQr = User::whereNotNull('topup_qr_code')->first();

        if ($existingQr && $existingQr->topup_qr_code) {
            // Use Storage facade to delete from public disk
            \Storage::disk('public')->delete($existingQr->topup_qr_code);
            $existingQr->update(['topup_qr_code' => null]);
            return back()->with('success', 'QR Code berhasil dihapus.');
        }

        return back()->with('error', 'Tidak ada QR Code untuk dihapus.');
    }
}

