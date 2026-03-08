<?php

namespace App\Controllers;

use App\Models\ClientModel;

class Clients extends BaseController
{
    protected $clientModel;
    protected $userId;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        // Session user_id will be available in methods, but good to init helper here if needed
    }

    public function index()
    {
        $userId = session()->get('id');
        $search = $this->request->getGet('q');

        $builder = $this->clientModel->where('user_id', $userId);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('client_name', $search)
                ->orLike('client_phone', $search)
                ->orLike('client_email', $search)
            ->groupEnd();
        }

        $builder->orderBy('client_name', 'ASC');

        $data = [
            'title' => 'Data Pelanggan',
            'clients' => $builder->paginate(20),
            'pager' => $this->clientModel->pager,
            'totalClients' => $this->clientModel->where('user_id', $userId)->countAllResults(),
            'search' => $search,
        ];

        return view('clients/index', $data);
    }

    public function create()
    {
        return view('clients/create', ['title' => 'Tambah Pelanggan']);
    }

    public function store()
    {
        $userId = session()->get('id');

        if (! $this->validate($this->clientModel->validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->clientModel->save([
            'user_id' => $userId,
            'client_name' => $this->request->getVar('client_name'),
            'client_address' => $this->request->getVar('client_address'),
            'client_phone' => $this->request->getVar('client_phone'),
            'client_email' => $this->request->getVar('client_email'),
        ]);

        return redirect()->to('/clients')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function store_ajax()
    {
        $userId = session()->get('id');

        // Validation for AJAX
        if (! $this->validate($this->clientModel->validationRules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
                'csrf_token' => csrf_hash()
            ]);
        }

        try {
            $data = [
                'user_id' => $userId,
                'client_name' => $this->request->getVar('client_name'),
                'client_address' => $this->request->getVar('client_address'),
                'client_phone' => $this->request->getVar('client_phone'),
                'client_email' => $this->request->getVar('client_email'),
            ];

            $this->clientModel->save($data);
            $newId = $this->clientModel->insertID();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pelanggan berhasil ditambahkan.',
                'data' => [
                    'id' => $newId,
                    'client_name' => $data['client_name']
                ],
                'csrf_token' => csrf_hash() // Return new token
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => ['exception' => $e->getMessage()],
                'csrf_token' => csrf_hash()
            ]);
        }
    }

    public function show($id)
    {
        $userId = session()->get('id');
        $client = $this->clientModel->where('user_id', $userId)->find($id);

        if (!$client) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Get invoices for this client (optional, but good for detail)
        $invoiceModel = new \App\Models\InvoiceModel();
        $invoices = $invoiceModel->where('client_id', $id)->orderBy('date_issued', 'DESC')->findAll();

        return view('clients/show', [
            'title' => 'Detail Pelanggan',
            'client' => $client,
            'invoices' => $invoices
        ]);
    }

    public function edit($id)
    {
        $userId = session()->get('id');
        $client = $this->clientModel->where('user_id', $userId)->find($id);

        if (!$client) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('clients/edit', [
            'title' => 'Edit Pelanggan',
            'client' => $client
        ]);
    }

    public function update($id)
    {
        $userId = session()->get('id');
        $client = $this->clientModel->where('user_id', $userId)->find($id);

        if (!$client) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->clientModel->validationRules)) {
             return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->clientModel->update($id, [
            'client_name' => $this->request->getVar('client_name'),
            'client_address' => $this->request->getVar('client_address'),
            'client_phone' => $this->request->getVar('client_phone'),
            'client_email' => $this->request->getVar('client_email'),
        ]);

        return redirect()->to('/clients')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $userId = session()->get('id');
        $client = $this->clientModel->where('user_id', $userId)->find($id);

        if (!$client) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->clientModel->delete($id);
        return redirect()->to('/clients')->with('success', 'Pelanggan dihapus.');
    }
}
