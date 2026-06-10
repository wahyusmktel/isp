import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  runApp(const Tim7NetApp());
}

class Tim7NetApp extends StatelessWidget {
  const Tim7NetApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Tim-7 Net',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF0284C7),
          brightness: Brightness.light,
        ),
        scaffoldBackgroundColor: const Color(0xFFF6F8FB),
        fontFamily: 'Roboto',
      ),
      home: const SessionGate(),
    );
  }
}

class ApiConfig {
  static const baseUrl = 'https://tim-7.net/api/mobile';
}

class MobileApi {
  const MobileApi();

  Future<CustomerSession> login(String customerNumber) async {
    final response = await http.post(
      Uri.parse('${ApiConfig.baseUrl}/customer/login'),
      headers: const {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({'customer_number': customerNumber}),
    );

    final json = _decode(response);
    if (response.statusCode >= 400 || json['success'] != true) {
      throw ApiException(json['message']?.toString() ?? 'Login gagal.');
    }

    return CustomerSession.fromJson(json);
  }

  Future<CustomerSession> invoices(String customerNumber) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}/customer/invoices').replace(
      queryParameters: {
        'customer_number': customerNumber,
        'limit': '25',
      },
    );
    final response = await http.get(uri, headers: const {'Accept': 'application/json'});

    final json = _decode(response);
    if (response.statusCode >= 400 || json['success'] != true) {
      throw ApiException(json['message']?.toString() ?? 'Gagal memuat tagihan.');
    }

    return CustomerSession.fromJson(json);
  }

  Map<String, dynamic> _decode(http.Response response) {
    try {
      return jsonDecode(response.body) as Map<String, dynamic>;
    } catch (_) {
      throw const ApiException('Respons server tidak valid.');
    }
  }
}

class ApiException implements Exception {
  const ApiException(this.message);
  final String message;

  @override
  String toString() => message;
}

class CustomerSession {
  const CustomerSession({
    required this.customer,
    required this.summary,
    required this.invoices,
  });

  final Customer customer;
  final BillingSummary summary;
  final List<Invoice> invoices;

  factory CustomerSession.fromJson(Map<String, dynamic> json) {
    return CustomerSession(
      customer: Customer.fromJson(json['customer'] as Map<String, dynamic>),
      summary: BillingSummary.fromJson(json['summary'] as Map<String, dynamic>),
      invoices: ((json['invoices'] as List?) ?? const [])
          .map((item) => Invoice.fromJson(item as Map<String, dynamic>))
          .toList(),
    );
  }
}

class Customer {
  const Customer({
    required this.customerNumber,
    required this.name,
    required this.status,
    required this.statusLabel,
    required this.packageName,
    required this.billingDate,
  });

  final String customerNumber;
  final String name;
  final String status;
  final String statusLabel;
  final String packageName;
  final int billingDate;

  String get firstName => name.trim().split(RegExp(r'\s+')).first;

  factory Customer.fromJson(Map<String, dynamic> json) {
    return Customer(
      customerNumber: json['customer_number']?.toString() ?? '',
      name: json['name']?.toString() ?? 'Pelanggan',
      status: json['status']?.toString() ?? 'aktif',
      statusLabel: json['status_label']?.toString() ?? 'Aktif',
      packageName: json['package_name']?.toString() ?? 'Belum ada paket',
      billingDate: int.tryParse(json['billing_date']?.toString() ?? '') ?? 1,
    );
  }
}

class BillingSummary {
  const BillingSummary({
    required this.total,
    required this.unpaid,
    required this.paid,
    required this.overdue,
    required this.outstandingAmount,
  });

  final int total;
  final int unpaid;
  final int paid;
  final int overdue;
  final int outstandingAmount;

  factory BillingSummary.fromJson(Map<String, dynamic> json) {
    return BillingSummary(
      total: _intValue(json['total']),
      unpaid: _intValue(json['unpaid']),
      paid: _intValue(json['paid']),
      overdue: _intValue(json['overdue']),
      outstandingAmount: _intValue(json['outstanding_amount']),
    );
  }

  static int _intValue(Object? value) => int.tryParse(value?.toString() ?? '') ?? 0;
}

class Invoice {
  const Invoice({
    required this.invoiceNumber,
    required this.billingPeriodLabel,
    required this.amount,
    required this.status,
    required this.statusLabel,
    required this.dueDateLabel,
    this.paymentMethod,
    this.notes,
  });

  final String invoiceNumber;
  final String billingPeriodLabel;
  final int amount;
  final String status;
  final String statusLabel;
  final String dueDateLabel;
  final String? paymentMethod;
  final String? notes;

  factory Invoice.fromJson(Map<String, dynamic> json) {
    return Invoice(
      invoiceNumber: json['invoice_number']?.toString() ?? '-',
      billingPeriodLabel: json['billing_period_label']?.toString() ?? '-',
      amount: int.tryParse(json['amount']?.toString() ?? '') ?? 0,
      status: json['status']?.toString() ?? 'unpaid',
      statusLabel: json['status_label']?.toString() ?? 'Belum Dibayar',
      dueDateLabel: json['due_date_label']?.toString() ?? '-',
      paymentMethod: json['payment_method']?.toString(),
      notes: json['notes']?.toString(),
    );
  }
}

class SessionGate extends StatefulWidget {
  const SessionGate({super.key});

  @override
  State<SessionGate> createState() => _SessionGateState();
}

class _SessionGateState extends State<SessionGate> {
  String? _customerNumber;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _restore();
  }

  Future<void> _restore() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _customerNumber = prefs.getString('customer_number');
      _loading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    if (_customerNumber == null || _customerNumber!.isEmpty) {
      return LoginPage(onLoggedIn: _saveSession);
    }

    return BillingPage(
      customerNumber: _customerNumber!,
      onLogout: _logout,
    );
  }

  Future<void> _saveSession(String customerNumber) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('customer_number', customerNumber);
    setState(() => _customerNumber = customerNumber);
  }

  Future<void> _logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('customer_number');
    setState(() => _customerNumber = null);
  }
}

class LoginPage extends StatefulWidget {
  const LoginPage({super.key, required this.onLoggedIn});

  final ValueChanged<String> onLoggedIn;

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _api = const MobileApi();
  final _formKey = GlobalKey<FormState>();
  final _controller = TextEditingController();
  bool _submitting = false;
  String? _error;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    FocusScope.of(context).unfocus();
    setState(() {
      _submitting = true;
      _error = null;
    });

    try {
      final session = await _api.login(_controller.text.trim());
      widget.onLoggedIn(session.customer.customerNumber);
    } on ApiException catch (error) {
      setState(() => _error = error.message);
    } catch (_) {
      setState(() => _error = 'Tidak bisa terhubung ke server.');
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(22, 28, 22, 22),
          children: [
            const SizedBox(height: 24),
            Center(
              child: Container(
                width: 76,
                height: 76,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: const [
                    BoxShadow(color: Color(0x1A0F172A), blurRadius: 24, offset: Offset(0, 12)),
                  ],
                ),
                child: const Icon(Icons.wifi_rounded, size: 42, color: Color(0xFF0284C7)),
              ),
            ),
            const SizedBox(height: 26),
            Text(
              'Portal Pelanggan',
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                    fontWeight: FontWeight.w800,
                    color: const Color(0xFF111827),
                  ),
            ),
            const SizedBox(height: 8),
            Text(
              'Masuk untuk melihat tagihan dan status layanan Tim-7 Net.',
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: const Color(0xFF64748B)),
            ),
            const SizedBox(height: 34),
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: const Color(0xFFE2E8F0)),
              ),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Nomor Pelanggan',
                      style: TextStyle(fontWeight: FontWeight.w700, color: Color(0xFF334155)),
                    ),
                    const SizedBox(height: 10),
                    TextFormField(
                      controller: _controller,
                      keyboardType: TextInputType.text,
                      textInputAction: TextInputAction.done,
                      decoration: InputDecoration(
                        hintText: 'Contoh: 12345',
                        prefixIcon: const Icon(Icons.badge_outlined),
                        filled: true,
                        fillColor: const Color(0xFFF8FAFC),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(14),
                          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                        ),
                      ),
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Nomor pelanggan wajib diisi.';
                        }
                        return null;
                      },
                      onFieldSubmitted: (_) => _submit(),
                    ),
                    if (_error != null) ...[
                      const SizedBox(height: 14),
                      ErrorBanner(message: _error!),
                    ],
                    const SizedBox(height: 18),
                    SizedBox(
                      width: double.infinity,
                      height: 52,
                      child: FilledButton.icon(
                        onPressed: _submitting ? null : _submit,
                        icon: _submitting
                            ? const SizedBox(
                                width: 18,
                                height: 18,
                                child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                              )
                            : const Icon(Icons.arrow_forward_rounded),
                        label: Text(_submitting ? 'Memeriksa...' : 'Masuk'),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),
            const Text(
              'Lupa ID pelanggan? Hubungi customer service Tim-7 Net.',
              textAlign: TextAlign.center,
              style: TextStyle(color: Color(0xFF94A3B8), fontSize: 12),
            ),
          ],
        ),
      ),
    );
  }
}

class BillingPage extends StatefulWidget {
  const BillingPage({
    super.key,
    required this.customerNumber,
    required this.onLogout,
  });

  final String customerNumber;
  final VoidCallback onLogout;

  @override
  State<BillingPage> createState() => _BillingPageState();
}

class _BillingPageState extends State<BillingPage> {
  final _api = const MobileApi();
  late Future<CustomerSession> _future;

  @override
  void initState() {
    super.initState();
    _future = _api.invoices(widget.customerNumber);
  }

  void _refresh() {
    setState(() => _future = _api.invoices(widget.customerNumber));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tagihan'),
        centerTitle: false,
        actions: [
          IconButton(
            tooltip: 'Muat ulang',
            onPressed: _refresh,
            icon: const Icon(Icons.refresh_rounded),
          ),
          IconButton(
            tooltip: 'Keluar',
            onPressed: widget.onLogout,
            icon: const Icon(Icons.logout_rounded),
          ),
        ],
      ),
      body: FutureBuilder<CustomerSession>(
        future: _future,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            final message = snapshot.error is ApiException
                ? (snapshot.error as ApiException).message
                : 'Tidak bisa memuat data.';
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(22),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    ErrorBanner(message: message),
                    const SizedBox(height: 14),
                    FilledButton.icon(
                      onPressed: _refresh,
                      icon: const Icon(Icons.refresh_rounded),
                      label: const Text('Coba lagi'),
                    ),
                  ],
                ),
              ),
            );
          }

          final session = snapshot.data!;
          return RefreshIndicator(
            onRefresh: () async => _refresh(),
            child: ListView(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
              children: [
                HeaderCard(session: session),
                const SizedBox(height: 14),
                SummaryRow(summary: session.summary),
                const SizedBox(height: 20),
                Text(
                  'Riwayat Tagihan',
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF111827),
                      ),
                ),
                const SizedBox(height: 10),
                if (session.invoices.isEmpty)
                  const EmptyInvoices()
                else
                  ...session.invoices.map((invoice) => InvoiceTile(invoice: invoice)),
              ],
            ),
          );
        },
      ),
    );
  }
}

class HeaderCard extends StatelessWidget {
  const HeaderCard({super.key, required this.session});

  final CustomerSession session;

  @override
  Widget build(BuildContext context) {
    final customer = session.customer;
    final isActive = customer.status.toLowerCase() == 'aktif';

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFF0369A1),
        borderRadius: BorderRadius.circular(22),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  'Halo, ${customer.firstName}',
                  style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                        color: Colors.white,
                        fontWeight: FontWeight.w900,
                      ),
                ),
              ),
              StatusPill(
                label: customer.statusLabel,
                color: isActive ? const Color(0xFF16A34A) : const Color(0xFFDC2626),
                light: true,
              ),
            ],
          ),
          const SizedBox(height: 16),
          InfoLine(icon: Icons.confirmation_number_outlined, label: 'ID', value: customer.customerNumber),
          InfoLine(icon: Icons.router_outlined, label: 'Paket', value: customer.packageName),
          InfoLine(icon: Icons.event_available_outlined, label: 'Tagihan', value: 'Setiap tanggal ${customer.billingDate}'),
        ],
      ),
    );
  }
}

class InfoLine extends StatelessWidget {
  const InfoLine({
    super.key,
    required this.icon,
    required this.label,
    required this.value,
  });

  final IconData icon;
  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: 8),
      child: Row(
        children: [
          Icon(icon, color: const Color(0xFFBAE6FD), size: 18),
          const SizedBox(width: 8),
          Text('$label: ', style: const TextStyle(color: Color(0xFFE0F2FE), fontWeight: FontWeight.w600)),
          Expanded(
            child: Text(
              value,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w700),
            ),
          ),
        ],
      ),
    );
  }
}

class SummaryRow extends StatelessWidget {
  const SummaryRow({super.key, required this.summary});

  final BillingSummary summary;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: SummaryCard(
            label: 'Belum dibayar',
            value: '${summary.unpaid + summary.overdue}',
            icon: Icons.receipt_long_outlined,
            color: const Color(0xFFF59E0B),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: SummaryCard(
            label: 'Outstanding',
            value: rupiah(summary.outstandingAmount),
            icon: Icons.account_balance_wallet_outlined,
            color: const Color(0xFF0F766E),
          ),
        ),
      ],
    );
  }
}

class SummaryCard extends StatelessWidget {
  const SummaryCard({
    super.key,
    required this.label,
    required this.value,
    required this.icon,
    required this.color,
  });

  final String label;
  final String value;
  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 118,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 24),
          const Spacer(),
          Text(label, style: const TextStyle(color: Color(0xFF64748B), fontSize: 12)),
          const SizedBox(height: 3),
          FittedBox(
            alignment: Alignment.centerLeft,
            fit: BoxFit.scaleDown,
            child: Text(
              value,
              style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: Color(0xFF0F172A)),
            ),
          ),
        ],
      ),
    );
  }
}

class InvoiceTile extends StatelessWidget {
  const InvoiceTile({super.key, required this.invoice});

  final Invoice invoice;

  @override
  Widget build(BuildContext context) {
    final color = switch (invoice.status) {
      'paid' => const Color(0xFF16A34A),
      'overdue' => const Color(0xFFDC2626),
      'cancelled' => const Color(0xFF64748B),
      _ => const Color(0xFFF59E0B),
    };

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      invoice.invoiceNumber,
                      style: const TextStyle(
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(invoice.billingPeriodLabel, style: const TextStyle(color: Color(0xFF64748B))),
                  ],
                ),
              ),
              StatusPill(label: invoice.statusLabel, color: color),
            ],
          ),
          const SizedBox(height: 14),
          Row(
            children: [
              Expanded(
                child: Text(
                  rupiah(invoice.amount),
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Color(0xFF111827)),
                ),
              ),
              const Icon(Icons.schedule_outlined, size: 16, color: Color(0xFF94A3B8)),
              const SizedBox(width: 5),
              Text(invoice.dueDateLabel, style: const TextStyle(color: Color(0xFF64748B), fontSize: 12)),
            ],
          ),
          if ((invoice.paymentMethod ?? '').isNotEmpty) ...[
            const SizedBox(height: 10),
            Text(
              'Metode: ${invoice.paymentMethod}',
              style: const TextStyle(color: Color(0xFF64748B), fontSize: 12),
            ),
          ],
        ],
      ),
    );
  }
}

class StatusPill extends StatelessWidget {
  const StatusPill({
    super.key,
    required this.label,
    required this.color,
    this.light = false,
  });

  final String label;
  final Color color;
  final bool light;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: light ? Colors.white.withValues(alpha: 0.18) : color.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        label.toUpperCase(),
        style: TextStyle(
          color: light ? Colors.white : color,
          fontSize: 10,
          fontWeight: FontWeight.w900,
        ),
      ),
    );
  }
}

class EmptyInvoices extends StatelessWidget {
  const EmptyInvoices({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(28),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: const Column(
        children: [
          Icon(Icons.receipt_long_outlined, size: 44, color: Color(0xFFCBD5E1)),
          SizedBox(height: 10),
          Text('Belum ada riwayat tagihan.', style: TextStyle(color: Color(0xFF64748B))),
        ],
      ),
    );
  }
}

class ErrorBanner extends StatelessWidget {
  const ErrorBanner({super.key, required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFFEF2F2),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFFECACA)),
      ),
      child: Row(
        children: [
          const Icon(Icons.error_outline_rounded, color: Color(0xFFDC2626), size: 18),
          const SizedBox(width: 8),
          Expanded(child: Text(message, style: const TextStyle(color: Color(0xFFB91C1C)))),
        ],
      ),
    );
  }
}

String rupiah(int amount) {
  return NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  ).format(amount);
}
