@extends('layouts.master')

@section('page-title', 'Realizar Operações')

@section('page-styles')

@endsection

@section('content')
@csrf
<div class="container mt-5">
    <h1 class="text-center text-dark font-weight-bold">Realizar Operações</h1>

    <div class="form-group">
        <label for="operation_select">Operação:</label>
        <select id="operation_select" class="form-control" required>
            @foreach ($actions as $value)
            <option value="{{ $value->id }}">{{ $value->name }}</option>
            @endforeach
        </select>
    </div>

    <div id="deposit_form" class="operation-forms">
        <div class="form-group">
            <label for="deposit_value">Valor:</label>
            <input type="number" id="deposit_value" class="form-control" step="0.01" placeholder="Digite o valor para depositar">
        </div>
        <button class="btn btn-success btn-block" id="btn_deposit">Depositar</button>
    </div>

    <div id="transfer_form" class="operation-forms d-none">
        <div class="form-group">
            <label for="transfer_value">Valor:</label>
            <input type="number" id="transfer_value" class="form-control" step="0.01" placeholder="Digite o valor da transferência">
        </div>
        <div class="form-group">
            <label for="transfer_account">Conta de destino:</label>
            <input type="text" id="transfer_account" class="form-control" placeholder="0000000000" minlength="10" maxlength="10" required pattern="[0-9\s]{1,10}">
        </div>
        <button class="btn btn-primary btn-block" id="btn_transfer">Transferir</button>
    </div>

    <div id="refund_table" class="operation-forms d-none">
        <h3 class="text-secondary font-weight-bold ">Operações para Estorno</h3>
        <div id="spinner" class="text-center d-none">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Carregando...</span>
            </div>
        </div>
        <table class="table table-bordered table-striped d-none">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Criado em</th>
                    <th>Concluído em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="refund_table_body">
                <!-- Linhas da tabela serão preenchidas dinamicamente -->
            </tbody>
        </table>
    </div>
</div>
@endsection


@section('page-script')
<script>
    let balance = parseFloat(@json(auth()->user()->account->balance));
    const accountId = @json(auth()->user()->account->id);
    document.addEventListener('DOMContentLoaded', () => {
        const operationSelect = document.getElementById('operation_select');
        const depositForm = document.getElementById('deposit_form');
        const transferForm = document.getElementById('transfer_form');
        const refundTable = document.getElementById('refund_table');
        const refundTableBody = document.getElementById('refund_table_body'); // Corpo da tabela
        const transferAccountInput = document.getElementById('transfer_account');
        const spinner = document.getElementById('spinner'); // Spinner de carregamento
        const transferAmount = document.getElementById('transfer_value');

        transferAmount.addEventListener('input', (e) => {
            const amount = parseFloat(e.target.value);
            if (amount > balance) {
                e.target.value = balance.toFixed(2);
            }
        })


        document.getElementById('transfer_account').addEventListener('input', (e) => {
            const input = event.target;
            input.value = input.value.replace(/[^0-9]/g, '');
        })


        operationSelect.addEventListener('change', () => {
            const selectedOperation = operationSelect.value;

            // Esconde todos os formulários
            document.querySelectorAll('.operation-forms').forEach(form => form.classList.add('d-none'));

            if (selectedOperation == 1) { // Depositar
                depositForm.classList.remove('d-none');
            } else if (selectedOperation == 2) { // Transferir
                transferForm.classList.remove('d-none');
            } else if (selectedOperation == 3) { // Estornar
                refundTable.classList.remove('d-none');
                // Limpa a tabela e exibe o spinner
                refundTableBody.innerHTML = '';
                spinner.classList.remove('d-none');

                // Faz o fetch para buscar as operações que podem ser estornadas
                fetch('/fetch-refunds', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(error => Promise.reject(error));
                        }
                        return response.json();
                    })
                    .then(data => {
                        spinner.classList.add('d-none'); // Remove o spinner
                        document.querySelector('.table').classList.remove('d-none'); // Exibe a tabela


                        if (data.length === 0) {
                            refundTableBody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhuma operação disponível para estorno.</td></tr>';
                            return;
                        }

                        // Preenche a tabela com os dados retornados
                        data.data.forEach(record => {
                            record.amount = parseFloat(record.amount)

                            const formattedCreatedAt = new Date(record.created_at).toLocaleDateString('pt-BR');
                            const formattedProcessedAt = record.processed_at ? new Date(record.processed_at).toLocaleDateString('pt-BR') : null;

                            const row = document.createElement('tr');

                            let htmlTable = `
                        <td>${record.id}</td>
                        <td>${record.description}</td>
                        <td class="${record.amount > 0 ? 'positive-value' : 'negative-value'}"> R$ ${record.amount.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}</td>
                        <td>${formattedCreatedAt}</td>
                        <td>${formattedProcessedAt|| 'Pendente'}</td>`;
                            row.innerHTML = `
                        <td>${record.id}</td>
                        <td>${record.description}</td>
                        <td class="${record.amount > 0 ? 'positive-value' : 'negative-value'}"> R$ ${record.amount.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}</td>
                        <td>${formattedCreatedAt}</td>
                        <td>${formattedProcessedAt|| 'Pendente'}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" data-id="${record.operation_id}">Estornar</button>
                        </td>
                    `;
                            refundTableBody.appendChild(row);
                        });

                        // Adiciona evento de clique para os botões de estorno
                        refundTableBody.querySelectorAll('.btn-danger').forEach(button => {
                            button.addEventListener('click', (e) => {
                                const operationData = data.data.find(item => item.operation_id == e.target.getAttribute('data-id'));

                                const operationId = button.getAttribute('data-id');
                                let valorEstorno = parseFloat(operationData.amount);
                                if (valorEstorno > balance && operationData.fulfilled) {
                                    e.target.disabled = true;
                                    e.target.classList.add('disabled');
                                    return toastr.error('Saldo insuficiente para realizar o estorno desta operação.');
                                }
                                handleRefund(operationId);
                            });
                        });
                    })

                    .catch(error => {
                        spinner.classList.add('d-none'); // Remove o spinner em caso de erro
                        toastr.error(error.message || 'Erro ao buscar operações para estorno.');
                    });
            }
        });


        function updateBalanceAccount() {
            fetch('/balance/update', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            }).then(response => {
                console.log(response.ok, 'teste')
                if (!response.ok) {
                    return response.json().then(error => Promise.reject(error));
                }
                return response.json();
            }).then(data => {
                document.getElementById('account_balance').innerText = parseFloat(data.data.balance)
            }).catch(error => {
                toastr.error('Erro ao atualizar saldo.');
                reload()
            });
        }

        function handleRefund(operationId) {
            // Faz o fetch para estornar a operação
            fetch(`/refund/${operationId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => Promise.reject(error));
                    }
                    return response.json();
                })
                .then(data => {
                    toastr.success('Estorno realizado com sucesso!');
                    updateBalanceAccount()
                    // Atualiza a tabela para remover o item estornado
                    operationSelect.dispatchEvent(new Event('change'));
                })
                .catch(error => {
                    toastr.error(error.message || 'Erro ao realizar o estorno.');
                });
        }

        document.getElementById('btn_deposit').addEventListener('click', () => {
            const amount = parseFloat(document.getElementById('deposit_value').value);

            if (isNaN(amount) || amount <= 0) {
                toastr.error('O valor deve ser maior que zero.');
                return;
            }

            fetch('/deposit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value // Token CSRF do Laravel
                    },
                    body: JSON.stringify({
                        amount,
                        'actionId': operationSelect.value,
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => Promise.reject(error));
                    }
                    return response.json();
                })
                .then(result => {
                    toastr.success('Depósito realizado com sucesso!');
                })
                .catch(error => {
                    console.error(error);
                    toastr.error(error.message || 'Erro ao realizar depósito.');
                });
        });

        document.getElementById('btn_transfer').addEventListener('click', () => {
            const amount = parseFloat(document.getElementById('transfer_value').value);
            const toAccountNumber = document.getElementById('transfer_account').value.replace(/-/g, '').trim(); // Remove o hífen

            if (isNaN(amount) || amount <= 0) {
                toastr.error('O valor deve ser maior que zero.');
                return;
            }

            if (!/^\d{10}$/.test(toAccountNumber)) {
                toastr.error('Número da conta inválido. Use o formato 0000000000.');
                return;
            }

            fetch('/transfer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value // Token CSRF do Laravel
                    },
                    body: JSON.stringify({
                        amount,
                        toAccountNumber,
                        'actionId': operationSelect.value,
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => Promise.reject(error));
                    }
                    return response.json();
                })
                .then(result => {
                    updateBalanceAccount();
                    toastr.success('Transferência realizada com sucesso!');
                })
                .catch(error => {
                    console.error(error);
                    toastr.error(error.message || 'Erro ao realizar transferência.');
                });
        });
    });
</script>
@endsection