@extends('layouts.master')

@section('title', 'Tabela de Operações')

@section('page-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .disabled-row {
        pointer-events: none;
        opacity: 0.6;
    }

    .tooltip-inner {
        background-color: #343a40;
        color: #fff;
    }

</style>
@endsection

@section('content')
<div class="container-fluid my-4">
    <h2 class="mb-4">Tabela de Operações</h2>

    <!-- Filtros -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="filter-date">Data</label>
            <input type="text" id="filter-date" class="form-control flatpickr" placeholder="Selecione um intervalo de até 30 dias.">
        </div>

        <div class="col-md-3">
            <label for="filter-type">Operação</label>
            <select id="filter-type" class="form-control">
                <option value="">Todos</option>
                @foreach ($actions as $value)
                <option value="{{ $value->id}}">{{ $value->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="filter-status">Status</label>
            <select id="filter-status" class="form-control">
                <option value="">Todos</option>
                <option value="pendente">Pendentes</option>
                <option value="concluido">Concluídas</option>
            </select>
        </div>

        <div class="col-auto ml-auto align-self-end mt-2 mt-md-0">
            <button type="button" class="btn btn-primary" id="apply-filters">Filtrar</button>
        </div>
    </div>

    <!-- Tabela -->
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Realizado em</th>
                <th>Processado em</th>
            </tr>
        </thead>
        <tbody id="table-body">
            <!-- Os dados serão inseridos aqui dinamicamente -->
        </tbody>
    </table>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
<script>
    const data = @json($operations);
    document.addEventListener('DOMContentLoaded', () => {
        // Configuração do Flatpickr
        flatpickr('.flatpickr', {
            locale: 'pt', // Tradução para português
            mode: 'range', // Ativa o modo de intervalo de datas
            dateFormat: 'd-m-Y', // Formato de data
            maxDate: 'today', // Garante que o usuário não possa selecionar datas futuras
            onClose: function(selectedDates) {
                if (selectedDates.length === 2) { // Verifica se um intervalo foi selecionado
                    const diffInDays = Math.ceil((selectedDates[1] - selectedDates[0]) / (1000 * 60 * 60 * 24));
                    if (diffInDays > 30) {
                        toast.error('Por favor, selecione um intervalo de no máximo 30 dias.', 'Error', {
                            closeButton: true,
                            progressBar: true,
                        });
                        this.clear(); // Limpa a seleção
                    }
                }
            }
        });


        const tableBody = document.getElementById('table-body');

        const renderTable = (filteredData) => {
            tableBody.innerHTML = '';

            filteredData.forEach(item => {
                const row = document.createElement('tr');
                row.className = item.realizado_em === null ? 'disabled-row' : '';
                item.amount = parseFloat(item.amount);

                const formattedCreatedAt = new Date(item.created_at).toLocaleDateString('pt-BR');
                const formattedProcessedAt = item.processed_at ? new Date(item.processed_at).toLocaleDateString('pt-BR') : null;

                row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.description}</td>
                    <td class="${item.amount > 0 ? 'positive-value' : 'negative-value'}"> R$ ${item.amount.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}</td>
                    <td>${formattedCreatedAt}</td>
                    <td>${formattedProcessedAt ?? `<span data-toggle="tooltip" title="Processando operação">Pendente</span>`}</td>
                `;

                tableBody.appendChild(row);
            });

        };

        renderTable(data);

        function parseDate(dateString, endOfDay = false) {
            const [day, month, year] = dateString.split('-').map(Number);
            return endOfDay ?
                new Date(year, month - 1, day, 23, 59, 59) // Final do dia
                :
                new Date(year, month - 1, day, 0, 0, 0); // Início do dia
        }


        document.getElementById('apply-filters').addEventListener('click', () => {
            const selectedDates = document.getElementById('filter-date').value.split(' até ');;
            const selectedType = document.getElementById('filter-type').value;
            const selectedStatus = document.getElementById('filter-status').value;

            let filteredData = data;

            if (selectedDates.length === 2) {
                const [startDate, endDate] = [
                    parseDate(selectedDates[0]), // Início do intervalo
                    parseDate(selectedDates[1], true), // Final do intervalo
                ];

                filteredData = filteredData.filter(item => {
                    const createdAt = new Date(item.created_at); // Supondo que created_at já esteja no formato válido
                    return createdAt >= startDate && createdAt <= endDate;
                });
            } else if (selectedDates.length === 1 && selectedDates[0]) {
                const selectedDateStart = parseDate(selectedDates[0]); // Início do dia
                const selectedDateEnd = parseDate(selectedDates[0], true); // Final do dia

                filteredData = filteredData.filter(item => {
                    const createdAt = new Date(item.created_at);
                    return createdAt >= selectedDateStart && createdAt <= selectedDateEnd;

                });
            }
            if (selectedType) {
                filteredData = filteredData.filter(item => item.action_id == selectedType);
            }

            if (selectedStatus === 'pendente') {
                filteredData = filteredData.filter(item => item.processed_at == null);
            } else if (selectedStatus === 'concluido') {
                filteredData = filteredData.filter(item => item.processed_at != null);
            }

            renderTable(filteredData);
        });
    });
</script>
@endsection