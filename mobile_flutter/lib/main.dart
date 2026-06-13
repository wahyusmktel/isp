import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';

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
        filledButtonTheme: FilledButtonThemeData(
          style: FilledButton.styleFrom(
            minimumSize: const Size.fromHeight(50),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(14),
            ),
          ),
        ),
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
      queryParameters: {'customer_number': customerNumber, 'limit': '25'},
    );
    final response = await http.get(
      uri,
      headers: const {'Accept': 'application/json'},
    );

    final json = _decode(response);
    if (response.statusCode >= 400 || json['success'] != true) {
      throw ApiException(
        json['message']?.toString() ?? 'Gagal memuat tagihan.',
      );
    }

    return CustomerSession.fromJson(json);
  }

  Future<List<NewsItem>> news() async {
    final uri = Uri.parse(
      '${ApiConfig.baseUrl}/news',
    ).replace(queryParameters: {'limit': '5'});
    final response = await http.get(
      uri,
      headers: const {'Accept': 'application/json'},
    );

    final json = _decode(response);
    if (response.statusCode >= 400 || json['success'] != true) {
      throw ApiException(json['message']?.toString() ?? 'Gagal memuat berita.');
    }

    return ((json['news'] as List?) ?? const [])
        .map((item) => NewsItem.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  Future<StaffSession> staffLogin(String email, String password) async {
    final response = await http.post(
      Uri.parse('${ApiConfig.baseUrl}/staff/login'),
      headers: const {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({'email': email, 'password': password}),
    );

    final json = _decode(response);
    if (response.statusCode >= 400 || json['success'] != true) {
      throw ApiException(
          json['message']?.toString() ?? 'Login admin/operator gagal.');
    }

    return StaffSession.fromJson(json);
  }

  Future<StaffDashboardData> staffDashboard(String token) async {
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/staff/dashboard'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    final json = _decode(response);
    if (response.statusCode >= 400 || json['success'] != true) {
      throw ApiException(
        json['message']?.toString() ?? 'Gagal memuat dashboard.',
      );
    }

    return StaffDashboardData.fromJson(json);
  }

  Future<StaffInvoiceResponse> staffInvoices(
      String token, String period) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}/staff/invoices').replace(
      queryParameters: {
        'period': period,
        'limit': '100',
      },
    );
    final response = await http.get(uri, headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });

    final json = _decode(response);
    if (response.statusCode >= 400 || json['success'] != true) {
      throw ApiException(
          json['message']?.toString() ?? 'Gagal memuat tagihan.');
    }

    return StaffInvoiceResponse.fromJson(json);
  }

  Future<StaffInvoice> updateStaffInvoiceStatus(
      String token, int invoiceId, String status) async {
    final response = await http.patch(
      Uri.parse('${ApiConfig.baseUrl}/staff/invoices/$invoiceId/status'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({'status': status}),
    );

    final json = _decode(response);
    if (response.statusCode >= 400 || json['success'] != true) {
      throw ApiException(
          json['message']?.toString() ?? 'Gagal mengubah status.');
    }

    return StaffInvoice.fromJson(json['invoice'] as Map<String, dynamic>);
  }

  Future<StaffInvoice> updateStaffPaymentMethod(
      String token, int invoiceId, String paymentMethod) async {
    final response = await http.patch(
      Uri.parse(
          '${ApiConfig.baseUrl}/staff/invoices/$invoiceId/payment-method'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({'payment_method': paymentMethod}),
    );

    final json = _decode(response);
    if (response.statusCode >= 400 || json['success'] != true) {
      throw ApiException(
          json['message']?.toString() ?? 'Gagal mengubah metode bayar.');
    }

    return StaffInvoice.fromJson(json['invoice'] as Map<String, dynamic>);
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

  String get firstName {
    final trimmed = name.trim();
    if (trimmed.isEmpty) return 'Pelanggan';
    return trimmed.split(RegExp(r'\s+')).first;
  }

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

  int get dueCount => unpaid + overdue;

  factory BillingSummary.fromJson(Map<String, dynamic> json) {
    return BillingSummary(
      total: _intValue(json['total']),
      unpaid: _intValue(json['unpaid']),
      paid: _intValue(json['paid']),
      overdue: _intValue(json['overdue']),
      outstandingAmount: _intValue(json['outstanding_amount']),
    );
  }

  static int _intValue(Object? value) =>
      int.tryParse(value?.toString() ?? '') ?? 0;
}

class Invoice {
  const Invoice({
    required this.id,
    required this.invoiceNumber,
    required this.billingPeriodLabel,
    required this.amount,
    required this.status,
    required this.statusLabel,
    required this.dueDateLabel,
    required this.pdfUrl,
    this.paymentMethod,
    this.notes,
  });

  final int id;
  final String invoiceNumber;
  final String billingPeriodLabel;
  final int amount;
  final String status;
  final String statusLabel;
  final String dueDateLabel;
  final String pdfUrl;
  final String? paymentMethod;
  final String? notes;

  factory Invoice.fromJson(Map<String, dynamic> json) {
    return Invoice(
      id: int.tryParse(json['id']?.toString() ?? '') ?? 0,
      invoiceNumber: json['invoice_number']?.toString() ?? '-',
      billingPeriodLabel: json['billing_period_label']?.toString() ?? '-',
      amount: int.tryParse(json['amount']?.toString() ?? '') ?? 0,
      status: json['status']?.toString() ?? 'unpaid',
      statusLabel: json['status_label']?.toString() ?? 'Belum Dibayar',
      dueDateLabel: json['due_date_label']?.toString() ?? '-',
      pdfUrl: json['pdf_url']?.toString() ?? '',
      paymentMethod: json['payment_method']?.toString(),
      notes: json['notes']?.toString(),
    );
  }
}

class StaffSession {
  const StaffSession({
    required this.token,
    required this.name,
    required this.email,
    required this.role,
  });

  final String token;
  final String name;
  final String email;
  final String role;

  factory StaffSession.fromJson(Map<String, dynamic> json) {
    final user = json['user'] as Map<String, dynamic>;
    return StaffSession(
      token: json['token']?.toString() ?? '',
      name: user['name']?.toString() ?? 'Staff',
      email: user['email']?.toString() ?? '',
      role: user['role']?.toString() ?? 'operator',
    );
  }
}

class StaffDashboardData {
  const StaffDashboardData({
    required this.summary,
    required this.revenueChart,
    required this.packageDistribution,
    required this.invoiceStatus,
    required this.upcomingDue,
    required this.network,
    required this.latestCustomers,
  });

  final StaffDashboardSummary summary;
  final List<RevenuePoint> revenueChart;
  final List<PackageDistributionItem> packageDistribution;
  final InvoiceStatusSummary invoiceStatus;
  final List<UpcomingDueItem> upcomingDue;
  final NetworkSummary network;
  final List<LatestCustomerItem> latestCustomers;

  factory StaffDashboardData.fromJson(Map<String, dynamic> json) {
    return StaffDashboardData(
      summary: StaffDashboardSummary.fromJson(
        (json['summary'] as Map?)?.cast<String, dynamic>() ?? const {},
      ),
      revenueChart: ((json['revenue_chart'] as List?) ?? const [])
          .map((item) => RevenuePoint.fromJson(item as Map<String, dynamic>))
          .toList(),
      packageDistribution: ((json['package_distribution'] as List?) ?? const [])
          .map((item) =>
              PackageDistributionItem.fromJson(item as Map<String, dynamic>))
          .toList(),
      invoiceStatus: InvoiceStatusSummary.fromJson(
        (json['invoice_status'] as Map?)?.cast<String, dynamic>() ?? const {},
      ),
      upcomingDue: ((json['upcoming_due'] as List?) ?? const [])
          .map((item) => UpcomingDueItem.fromJson(item as Map<String, dynamic>))
          .toList(),
      network: NetworkSummary.fromJson(
        (json['network'] as Map?)?.cast<String, dynamic>() ?? const {},
      ),
      latestCustomers: ((json['latest_customers'] as List?) ?? const [])
          .map((item) =>
              LatestCustomerItem.fromJson(item as Map<String, dynamic>))
          .toList(),
    );
  }
}

class StaffDashboardSummary {
  const StaffDashboardSummary({
    required this.totalCustomers,
    required this.activeCustomers,
    required this.newCustomersThisMonth,
    required this.revenueThisMonth,
    required this.paidInvoicesThisMonth,
    required this.overdueInvoices,
    required this.unpaidInvoices,
    required this.onlineRouters,
    required this.totalRouters,
    required this.totalPppoe,
    required this.mappedCustomers,
  });

  final int totalCustomers;
  final int activeCustomers;
  final int newCustomersThisMonth;
  final int revenueThisMonth;
  final int paidInvoicesThisMonth;
  final int overdueInvoices;
  final int unpaidInvoices;
  final int onlineRouters;
  final int totalRouters;
  final int totalPppoe;
  final int mappedCustomers;

  factory StaffDashboardSummary.fromJson(Map<String, dynamic> json) {
    return StaffDashboardSummary(
      totalCustomers: intValue(json['total_customers']),
      activeCustomers: intValue(json['active_customers']),
      newCustomersThisMonth: intValue(json['new_customers_this_month']),
      revenueThisMonth: intValue(json['revenue_this_month']),
      paidInvoicesThisMonth: intValue(json['paid_invoices_this_month']),
      overdueInvoices: intValue(json['overdue_invoices']),
      unpaidInvoices: intValue(json['unpaid_invoices']),
      onlineRouters: intValue(json['online_routers']),
      totalRouters: intValue(json['total_routers']),
      totalPppoe: intValue(json['total_pppoe']),
      mappedCustomers: intValue(json['mapped_customers']),
    );
  }
}

class RevenuePoint {
  const RevenuePoint({
    required this.month,
    required this.value,
    required this.label,
  });

  final String month;
  final int value;
  final String label;

  factory RevenuePoint.fromJson(Map<String, dynamic> json) {
    return RevenuePoint(
      month: json['month']?.toString() ?? '-',
      value: intValue(json['value']),
      label: json['label']?.toString() ?? '-',
    );
  }
}

class PackageDistributionItem {
  const PackageDistributionItem({
    required this.name,
    required this.total,
    required this.percentage,
  });

  final String name;
  final int total;
  final int percentage;

  factory PackageDistributionItem.fromJson(Map<String, dynamic> json) {
    return PackageDistributionItem(
      name: json['name']?.toString() ?? '-',
      total: intValue(json['total']),
      percentage: intValue(json['percentage']),
    );
  }
}

class InvoiceStatusSummary {
  const InvoiceStatusSummary({
    required this.total,
    required this.paid,
    required this.unpaid,
    required this.overdue,
    required this.cancelled,
  });

  final int total;
  final int paid;
  final int unpaid;
  final int overdue;
  final int cancelled;

  factory InvoiceStatusSummary.fromJson(Map<String, dynamic> json) {
    return InvoiceStatusSummary(
      total: intValue(json['total']),
      paid: intValue(json['paid']),
      unpaid: intValue(json['unpaid']),
      overdue: intValue(json['overdue']),
      cancelled: intValue(json['cancelled']),
    );
  }
}

class UpcomingDueItem {
  const UpcomingDueItem({
    required this.customerName,
    required this.packageName,
    required this.amount,
    required this.dueLabel,
    required this.days,
    required this.status,
  });

  final String customerName;
  final String packageName;
  final int amount;
  final String dueLabel;
  final int days;
  final String status;

  factory UpcomingDueItem.fromJson(Map<String, dynamic> json) {
    return UpcomingDueItem(
      customerName: json['customer_name']?.toString() ?? '-',
      packageName: json['package_name']?.toString() ?? '-',
      amount: intValue(json['amount']),
      dueLabel: json['due_label']?.toString() ?? '-',
      days: intValue(json['days']),
      status: json['status']?.toString() ?? 'unpaid',
    );
  }
}

class NetworkSummary {
  const NetworkSummary({
    required this.totalRouters,
    required this.onlineRouters,
    required this.totalPppoe,
    required this.mappedCustomers,
    required this.uptimePercentage,
  });

  final int totalRouters;
  final int onlineRouters;
  final int totalPppoe;
  final int mappedCustomers;
  final int uptimePercentage;

  factory NetworkSummary.fromJson(Map<String, dynamic> json) {
    return NetworkSummary(
      totalRouters: intValue(json['total_routers']),
      onlineRouters: intValue(json['online_routers']),
      totalPppoe: intValue(json['total_pppoe']),
      mappedCustomers: intValue(json['mapped_customers']),
      uptimePercentage: intValue(json['uptime_percentage']),
    );
  }
}

class LatestCustomerItem {
  const LatestCustomerItem({
    required this.name,
    required this.phone,
    required this.packageName,
    required this.joinDateLabel,
    required this.status,
    required this.statusLabel,
  });

  final String name;
  final String phone;
  final String packageName;
  final String joinDateLabel;
  final String status;
  final String statusLabel;

  factory LatestCustomerItem.fromJson(Map<String, dynamic> json) {
    return LatestCustomerItem(
      name: json['name']?.toString() ?? '-',
      phone: json['phone']?.toString() ?? '-',
      packageName: json['package_name']?.toString() ?? '-',
      joinDateLabel: json['join_date_label']?.toString() ?? '-',
      status: json['status']?.toString() ?? 'aktif',
      statusLabel: json['status_label']?.toString() ?? 'Aktif',
    );
  }
}

class StaffInvoiceResponse {
  const StaffInvoiceResponse({
    required this.period,
    required this.stats,
    required this.invoices,
  });

  final String period;
  final StaffInvoiceStats stats;
  final List<StaffInvoice> invoices;

  factory StaffInvoiceResponse.fromJson(Map<String, dynamic> json) {
    return StaffInvoiceResponse(
      period: json['period']?.toString() ?? '',
      stats: StaffInvoiceStats.fromJson(
          (json['stats'] as Map?)?.cast<String, dynamic>() ?? const {}),
      invoices: ((json['invoices'] as List?) ?? const [])
          .map((item) => StaffInvoice.fromJson(item as Map<String, dynamic>))
          .toList(),
    );
  }
}

class StaffInvoiceStats {
  const StaffInvoiceStats({
    required this.total,
    required this.unpaid,
    required this.paid,
    required this.overdue,
    required this.cancelled,
  });

  final int total;
  final int unpaid;
  final int paid;
  final int overdue;
  final int cancelled;

  factory StaffInvoiceStats.fromJson(Map<String, dynamic> json) {
    return StaffInvoiceStats(
      total: BillingSummary._intValue(json['total']),
      unpaid: BillingSummary._intValue(json['unpaid']),
      paid: BillingSummary._intValue(json['paid']),
      overdue: BillingSummary._intValue(json['overdue']),
      cancelled: BillingSummary._intValue(json['cancelled']),
    );
  }
}

class StaffInvoice {
  const StaffInvoice({
    required this.id,
    required this.invoiceNumber,
    required this.customerName,
    required this.customerNumber,
    required this.billingPeriodLabel,
    required this.amount,
    required this.status,
    required this.statusLabel,
    required this.dueDateLabel,
    required this.paymentMethod,
    required this.pdfUrl,
  });

  final int id;
  final String invoiceNumber;
  final String customerName;
  final String customerNumber;
  final String billingPeriodLabel;
  final int amount;
  final String status;
  final String statusLabel;
  final String dueDateLabel;
  final String paymentMethod;
  final String pdfUrl;

  factory StaffInvoice.fromJson(Map<String, dynamic> json) {
    return StaffInvoice(
      id: int.tryParse(json['id']?.toString() ?? '') ?? 0,
      invoiceNumber: json['invoice_number']?.toString() ?? '-',
      customerName: json['customer_name']?.toString() ?? '-',
      customerNumber: json['customer_number']?.toString() ?? '-',
      billingPeriodLabel: json['billing_period_label']?.toString() ?? '-',
      amount: int.tryParse(json['amount']?.toString() ?? '') ?? 0,
      status: json['status']?.toString() ?? 'unpaid',
      statusLabel: json['status_label']?.toString() ?? 'Belum Dibayar',
      dueDateLabel: json['due_date_label']?.toString() ?? '-',
      paymentMethod: json['payment_method']?.toString() ?? '',
      pdfUrl: json['pdf_url']?.toString() ?? '',
    );
  }
}

class NewsItem {
  const NewsItem({
    required this.title,
    required this.excerpt,
    required this.categoryLabel,
    required this.publishedAtLabel,
  });

  final String title;
  final String excerpt;
  final String categoryLabel;
  final String publishedAtLabel;

  factory NewsItem.fromJson(Map<String, dynamic> json) {
    return NewsItem(
      title: json['title']?.toString() ?? 'Informasi Tim-7 Net',
      excerpt: json['excerpt']?.toString() ?? '',
      categoryLabel: json['category_label']?.toString() ?? 'Umum',
      publishedAtLabel: json['published_at_label']?.toString() ?? '-',
    );
  }
}

class DashboardData {
  const DashboardData({required this.session, required this.news});

  final CustomerSession session;
  final List<NewsItem> news;
}

class SessionGate extends StatefulWidget {
  const SessionGate({super.key});

  @override
  State<SessionGate> createState() => _SessionGateState();
}

class _SessionGateState extends State<SessionGate> {
  String? _customerNumber;
  StaffSession? _staffSession;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _restore();
  }

  Future<void> _restore() async {
    final prefs = await SharedPreferences.getInstance();
    final staffToken = prefs.getString('staff_token');
    setState(() {
      _customerNumber = prefs.getString('customer_number');
      if (staffToken != null && staffToken.isNotEmpty) {
        _staffSession = StaffSession(
          token: staffToken,
          name: prefs.getString('staff_name') ?? 'Staff',
          email: prefs.getString('staff_email') ?? '',
          role: prefs.getString('staff_role') ?? 'operator',
        );
      }
      _loading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    if ((_customerNumber == null || _customerNumber!.isEmpty) &&
        _staffSession == null) {
      return LandingPage(
        onLoginTap: _openLogin,
        onStaffLoginTap: _openStaffLogin,
      );
    }

    if (_staffSession != null) {
      return StaffDashboardPage(
        session: _staffSession!,
        onLogout: _logout,
      );
    }

    return CustomerDashboardPage(
      customerNumber: _customerNumber!,
      onLogout: _logout,
    );
  }

  Future<void> _openLogin() async {
    final customerNumber = await Navigator.of(
      context,
    ).push<String>(MaterialPageRoute(builder: (_) => const LoginPage()));
    if (customerNumber == null || customerNumber.isEmpty) return;
    await _saveSession(customerNumber);
  }

  Future<void> _openStaffLogin() async {
    final session = await Navigator.of(context).push<StaffSession>(
      MaterialPageRoute(builder: (_) => const StaffLoginPage()),
    );
    if (session == null || session.token.isEmpty) return;
    await _saveStaffSession(session);
  }

  Future<void> _saveSession(String customerNumber) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('customer_number', customerNumber);
    await prefs.remove('staff_token');
    await prefs.remove('staff_name');
    await prefs.remove('staff_email');
    await prefs.remove('staff_role');
    setState(() {
      _customerNumber = customerNumber;
      _staffSession = null;
    });
  }

  Future<void> _saveStaffSession(StaffSession session) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('staff_token', session.token);
    await prefs.setString('staff_name', session.name);
    await prefs.setString('staff_email', session.email);
    await prefs.setString('staff_role', session.role);
    await prefs.remove('customer_number');
    setState(() {
      _customerNumber = null;
      _staffSession = session;
    });
  }

  Future<void> _logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('customer_number');
    await prefs.remove('staff_token');
    await prefs.remove('staff_name');
    await prefs.remove('staff_email');
    await prefs.remove('staff_role');
    setState(() {
      _customerNumber = null;
      _staffSession = null;
    });
  }
}

class LandingPage extends StatelessWidget {
  const LandingPage({
    super.key,
    required this.onLoginTap,
    required this.onStaffLoginTap,
  });

  final VoidCallback onLoginTap;
  final VoidCallback onStaffLoginTap;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(20, 18, 20, 28),
          children: [
            Row(
              children: [
                const BrandMark(size: 44),
                const SizedBox(width: 12),
                const Expanded(
                  child: Text(
                    'Tim-7 Net',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w900,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                ),
                TextButton(onPressed: onLoginTap, child: const Text('Login')),
              ],
            ),
            const SizedBox(height: 24),
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: const Color(0xFF082F49),
                borderRadius: BorderRadius.circular(28),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const StatusPill(
                    label: 'Fiber Optik Lampung',
                    color: Color(0xFF38BDF8),
                    light: true,
                  ),
                  const SizedBox(height: 22),
                  Text(
                    'Internet cepat, stabil, dan siap menemani rumah Anda.',
                    style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                          color: Colors.white,
                          fontWeight: FontWeight.w900,
                          height: 1.12,
                        ),
                  ),
                  const SizedBox(height: 12),
                  const Text(
                    'Nikmati koneksi tanpa batas dengan dukungan teknis responsif, paket terjangkau, dan portal pelanggan untuk pantau tagihan kapan saja.',
                    style: TextStyle(color: Color(0xFFBAE6FD), height: 1.55),
                  ),
                  const SizedBox(height: 24),
                  FilledButton.icon(
                    onPressed: onLoginTap,
                    icon: const Icon(Icons.login_rounded),
                    label: const Text('Masuk Portal Pelanggan'),
                  ),
                  const SizedBox(height: 10),
                  OutlinedButton.icon(
                    onPressed: onStaffLoginTap,
                    icon: const Icon(Icons.admin_panel_settings_outlined),
                    label: const Text('Login Admin / Operator'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: Colors.white,
                      side: const BorderSide(color: Color(0xFF7DD3FC)),
                      minimumSize: const Size.fromHeight(50),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14)),
                    ),
                  ),
                  const SizedBox(height: 22),
                  const Row(
                    children: [
                      Expanded(
                        child: LandingStat(value: '24/7', label: 'Support'),
                      ),
                      SizedBox(width: 10),
                      Expanded(
                        child: LandingStat(value: '100+', label: 'Mbps'),
                      ),
                      SizedBox(width: 10),
                      Expanded(
                        child: LandingStat(value: 'Fiber', label: 'Optik'),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 22),
            const SectionTitle(title: 'Layanan Tim-7 Net'),
            const SizedBox(height: 12),
            const FeatureTile(
              icon: Icons.speed_rounded,
              title: 'Koneksi cepat dan stabil',
              body:
                  'Jaringan fiber optik untuk streaming, belajar, meeting, dan kebutuhan bisnis.',
              color: Color(0xFF0284C7),
            ),
            const FeatureTile(
              icon: Icons.payments_outlined,
              title: 'Tagihan mudah dipantau',
              body:
                  'Pelanggan bisa melihat status tagihan langsung dari aplikasi mobile.',
              color: Color(0xFFF97316),
            ),
            const FeatureTile(
              icon: Icons.support_agent_rounded,
              title: 'Bantuan pelanggan',
              body:
                  'Tim support siap membantu saat ada kendala layanan atau pertanyaan tagihan.',
              color: Color(0xFF16A34A),
            ),
          ],
        ),
      ),
    );
  }
}

class LandingStat extends StatelessWidget {
  const LandingStat({super.key, required this.value, required this.label});

  final String value;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.10),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            value,
            style: const TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w900,
              fontSize: 18,
            ),
          ),
          Text(
            label,
            style: const TextStyle(color: Color(0xFFBAE6FD), fontSize: 11),
          ),
        ],
      ),
    );
  }
}

class FeatureTile extends StatelessWidget {
  const FeatureTile({
    super.key,
    required this.icon,
    required this.title,
    required this.body,
    required this.color,
  });

  final IconData icon;
  final String title;
  final String body;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          IconBubble(icon: icon, color: color),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF0F172A),
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  body,
                  style: const TextStyle(color: Color(0xFF64748B), height: 1.4),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

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
      if (mounted) Navigator.of(context).pop(session.customer.customerNumber);
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
      appBar: AppBar(title: const Text('Login Pelanggan')),
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(22, 20, 22, 22),
          children: [
            const SizedBox(height: 16),
            const Center(child: BrandMark(size: 76)),
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
              'Masuk untuk membuka dashboard, berita layanan, dan tagihan Tim-7 Net.',
              textAlign: TextAlign.center,
              style: Theme.of(
                context,
              ).textTheme.bodyMedium?.copyWith(color: const Color(0xFF64748B)),
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
                      style: TextStyle(
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF334155),
                      ),
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
                          borderSide: const BorderSide(
                            color: Color(0xFFE2E8F0),
                          ),
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
                    FilledButton.icon(
                      onPressed: _submitting ? null : _submit,
                      icon: _submitting
                          ? const SizedBox(
                              width: 18,
                              height: 18,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: Colors.white,
                              ),
                            )
                          : const Icon(Icons.arrow_forward_rounded),
                      label: Text(
                        _submitting ? 'Memeriksa...' : 'Masuk Dashboard',
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

class StaffLoginPage extends StatefulWidget {
  const StaffLoginPage({super.key});

  @override
  State<StaffLoginPage> createState() => _StaffLoginPageState();
}

class _StaffLoginPageState extends State<StaffLoginPage> {
  final _api = const MobileApi();
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _submitting = false;
  bool _obscure = true;
  String? _error;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
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
      final session = await _api.staffLogin(
        _emailController.text.trim(),
        _passwordController.text,
      );
      if (mounted) Navigator.of(context).pop(session);
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
      appBar: AppBar(title: const Text('Login Admin / Operator')),
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(22, 22, 22, 22),
          children: [
            const Center(child: BrandMark(size: 72)),
            const SizedBox(height: 24),
            Text(
              'Area Staff',
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                    fontWeight: FontWeight.w900,
                    color: const Color(0xFF111827),
                  ),
            ),
            const SizedBox(height: 8),
            const Text(
              'Masuk sebagai admin atau operator untuk mengelola tagihan pelanggan.',
              textAlign: TextAlign.center,
              style: TextStyle(color: Color(0xFF64748B)),
            ),
            const SizedBox(height: 28),
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
                  children: [
                    TextFormField(
                      controller: _emailController,
                      keyboardType: TextInputType.emailAddress,
                      textInputAction: TextInputAction.next,
                      decoration: const InputDecoration(
                        labelText: 'Email',
                        prefixIcon: Icon(Icons.email_outlined),
                      ),
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Email wajib diisi.';
                        }
                        if (!value.contains('@')) {
                          return 'Format email tidak valid.';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 14),
                    TextFormField(
                      controller: _passwordController,
                      obscureText: _obscure,
                      textInputAction: TextInputAction.done,
                      decoration: InputDecoration(
                        labelText: 'Password',
                        prefixIcon: const Icon(Icons.lock_outline_rounded),
                        suffixIcon: IconButton(
                          onPressed: () => setState(() => _obscure = !_obscure),
                          icon: Icon(_obscure
                              ? Icons.visibility_outlined
                              : Icons.visibility_off_outlined),
                        ),
                      ),
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Password wajib diisi.';
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
                    FilledButton.icon(
                      onPressed: _submitting ? null : _submit,
                      icon: _submitting
                          ? const SizedBox(
                              width: 18,
                              height: 18,
                              child: CircularProgressIndicator(
                                  strokeWidth: 2, color: Colors.white),
                            )
                          : const Icon(Icons.login_rounded),
                      label: Text(
                          _submitting ? 'Memeriksa...' : 'Masuk Area Staff'),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class StaffDashboardPage extends StatefulWidget {
  const StaffDashboardPage({
    super.key,
    required this.session,
    required this.onLogout,
  });

  final StaffSession session;
  final VoidCallback onLogout;

  @override
  State<StaffDashboardPage> createState() => _StaffDashboardPageState();
}

class _StaffDashboardPageState extends State<StaffDashboardPage> {
  final _api = const MobileApi();
  late Future<StaffDashboardData> _future;

  @override
  void initState() {
    super.initState();
    _future = _api.staffDashboard(widget.session.token);
  }

  void _refresh() {
    setState(() => _future = _api.staffDashboard(widget.session.token));
  }

  void _openInvoices() {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => StaffInvoicesPage(session: widget.session),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard Staff'),
        centerTitle: false,
        actions: [
          IconButton(
            tooltip: 'Muat ulang',
            onPressed: _refresh,
            icon: const Icon(Icons.refresh_rounded),
          ),
        ],
      ),
      body: FutureBuilder<StaffDashboardData>(
        future: _future,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            final message = snapshot.error is ApiException
                ? (snapshot.error as ApiException).message
                : 'Tidak bisa memuat dashboard.';
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

          final data = snapshot.data!;
          return RefreshIndicator(
            onRefresh: () async => _refresh(),
            child: ListView(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
              children: [
                StaffDashboardHero(session: widget.session),
                const SizedBox(height: 14),
                DashboardKpiGrid(summary: data.summary),
                const SizedBox(height: 16),
                QuickInvoiceAction(onTap: _openInvoices),
                const SizedBox(height: 16),
                RevenueChartCard(points: data.revenueChart),
                const SizedBox(height: 16),
                PackageDistributionCard(items: data.packageDistribution),
                const SizedBox(height: 16),
                InvoiceStatusCard(status: data.invoiceStatus),
                const SizedBox(height: 16),
                UpcomingDueCard(
                    items: data.upcomingDue, onViewAll: _openInvoices),
                const SizedBox(height: 16),
                NetworkCard(network: data.network),
                const SizedBox(height: 16),
                LatestCustomersCard(customers: data.latestCustomers),
              ],
            ),
          );
        },
      ),
    );
  }
}

class StaffDashboardHero extends StatelessWidget {
  const StaffDashboardHero({super.key, required this.session});

  final StaffSession session;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFF0F172A),
        borderRadius: BorderRadius.circular(24),
      ),
      child: Row(
        children: [
          const IconBubble(
              icon: Icons.dashboard_customize_outlined,
              color: Color(0xFF38BDF8)),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Halo, ${session.name}',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w900,
                      fontSize: 18),
                ),
                const SizedBox(height: 4),
                Text(
                  'Ringkasan operasional Tim-7 Net - ${session.role.toUpperCase()}',
                  style: const TextStyle(
                      color: Color(0xFFBAE6FD),
                      fontSize: 12,
                      fontWeight: FontWeight.w700),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class DashboardKpiGrid extends StatelessWidget {
  const DashboardKpiGrid({super.key, required this.summary});

  final StaffDashboardSummary summary;

  @override
  Widget build(BuildContext context) {
    final items = [
      KpiItem(
        label: 'Total Pelanggan',
        value: '${summary.totalCustomers}',
        sub: '+${summary.newCustomersThisMonth} bulan ini',
        icon: Icons.groups_outlined,
        color: const Color(0xFF0284C7),
      ),
      KpiItem(
        label: 'Pendapatan Bulan Ini',
        value: rupiahCompact(summary.revenueThisMonth),
        sub: '${summary.paidInvoicesThisMonth} tagihan lunas',
        icon: Icons.account_balance_wallet_outlined,
        color: const Color(0xFF16A34A),
      ),
      KpiItem(
        label: 'Tagihan Jatuh Tempo',
        value: '${summary.overdueInvoices}',
        sub: '${summary.unpaidInvoices} belum dibayar',
        icon: Icons.event_busy_outlined,
        color: const Color(0xFFF59E0B),
      ),
      KpiItem(
        label: 'Router Online',
        value: '${summary.onlineRouters}/${summary.totalRouters}',
        sub: '${summary.totalPppoe} PPPoE aktif',
        icon: Icons.router_outlined,
        color: const Color(0xFF7C3AED),
      ),
    ];

    return GridView.count(
      crossAxisCount: 2,
      childAspectRatio: 1.18,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 10,
      crossAxisSpacing: 10,
      children: items.map((item) => KpiCard(item: item)).toList(),
    );
  }
}

class KpiItem {
  const KpiItem({
    required this.label,
    required this.value,
    required this.sub,
    required this.icon,
    required this.color,
  });

  final String label;
  final String value;
  final String sub;
  final IconData icon;
  final Color color;
}

class KpiCard extends StatelessWidget {
  const KpiCard({super.key, required this.item});

  final KpiItem item;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          IconBubble(icon: item.icon, color: item.color),
          const Spacer(),
          FittedBox(
            alignment: Alignment.centerLeft,
            fit: BoxFit.scaleDown,
            child: Text(item.value,
                style: const TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w900,
                    color: Color(0xFF0F172A))),
          ),
          const SizedBox(height: 2),
          Text(item.label,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                  color: Color(0xFF475569),
                  fontWeight: FontWeight.w800,
                  fontSize: 12)),
          Text(item.sub,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(color: Color(0xFF94A3B8), fontSize: 11)),
        ],
      ),
    );
  }
}

class QuickInvoiceAction extends StatelessWidget {
  const QuickInvoiceAction({super.key, required this.onTap});

  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: const Color(0xFFEFF6FF),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFBFDBFE)),
      ),
      child: Row(
        children: [
          const IconBubble(
              icon: Icons.receipt_long_outlined, color: Color(0xFF2563EB)),
          const SizedBox(width: 12),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Aksi Cepat Tagihan',
                    style: TextStyle(
                        fontWeight: FontWeight.w900, color: Color(0xFF0F172A))),
                SizedBox(height: 3),
                Text('Kelola status pembayaran, metode bayar, dan PDF invoice.',
                    style: TextStyle(color: Color(0xFF64748B), fontSize: 12)),
              ],
            ),
          ),
          IconButton.filled(
            onPressed: onTap,
            icon: const Icon(Icons.arrow_forward_rounded),
            tooltip: 'Buka tagihan',
          ),
        ],
      ),
    );
  }
}

class RevenueChartCard extends StatelessWidget {
  const RevenueChartCard({super.key, required this.points});

  final List<RevenuePoint> points;

  @override
  Widget build(BuildContext context) {
    final maxValue =
        points.fold<int>(1, (max, item) => item.value > max ? item.value : max);
    return DashboardSectionCard(
      title: 'Pendapatan 6 Bulan Terakhir',
      subtitle: 'Total tagihan terkumpul per bulan',
      child: SizedBox(
        height: 170,
        child: points.isEmpty
            ? const Center(
                child: Text('Belum ada data pendapatan',
                    style: TextStyle(color: Color(0xFF94A3B8))))
            : Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: points.map((point) {
                  final heightFactor =
                      (point.value / maxValue).clamp(0.05, 1.0);
                  final isLast = point == points.last;
                  return Expanded(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 4),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.end,
                        children: [
                          Text(point.label,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                  fontSize: 9, color: Color(0xFF64748B))),
                          const SizedBox(height: 6),
                          Flexible(
                            child: FractionallySizedBox(
                              heightFactor: heightFactor,
                              alignment: Alignment.bottomCenter,
                              child: Container(
                                width: double.infinity,
                                decoration: BoxDecoration(
                                  color: isLast
                                      ? const Color(0xFF16A34A)
                                      : const Color(0xFFBBF7D0),
                                  borderRadius: const BorderRadius.vertical(
                                      top: Radius.circular(10)),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(height: 6),
                          Text(point.month,
                              style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: isLast
                                      ? FontWeight.w900
                                      : FontWeight.w600,
                                  color: isLast
                                      ? const Color(0xFF15803D)
                                      : const Color(0xFF94A3B8))),
                        ],
                      ),
                    ),
                  );
                }).toList(),
              ),
      ),
    );
  }
}

class PackageDistributionCard extends StatelessWidget {
  const PackageDistributionCard({super.key, required this.items});

  final List<PackageDistributionItem> items;

  @override
  Widget build(BuildContext context) {
    return DashboardSectionCard(
      title: 'Distribusi Paket',
      subtitle: 'Komposisi pelanggan aktif',
      child: items.isEmpty
          ? const EmptyDashboardText(text: 'Belum ada data paket')
          : Column(
              children: items.map((item) {
                return Padding(
                  padding: const EdgeInsets.only(bottom: 12),
                  child: MetricBar(
                    label: item.name,
                    trailing: '${item.total} (${item.percentage}%)',
                    percentage: item.percentage,
                    color: const Color(0xFF0284C7),
                  ),
                );
              }).toList(),
            ),
    );
  }
}

class InvoiceStatusCard extends StatelessWidget {
  const InvoiceStatusCard({super.key, required this.status});

  final InvoiceStatusSummary status;

  @override
  Widget build(BuildContext context) {
    final items = [
      ('Lunas', status.paid, const Color(0xFF16A34A)),
      ('Belum Bayar', status.unpaid, const Color(0xFFF59E0B)),
      ('Jatuh Tempo', status.overdue, const Color(0xFFDC2626)),
      ('Dibatalkan', status.cancelled, const Color(0xFF64748B)),
    ];

    return DashboardSectionCard(
      title: 'Status Tagihan Bulan Ini',
      subtitle: '${status.total} total tagihan bulan ini',
      child: Column(
        children: items.map((item) {
          final percentage =
              status.total > 0 ? ((item.$2 / status.total) * 100).round() : 0;
          return Padding(
            padding: const EdgeInsets.only(bottom: 12),
            child: MetricBar(
              label: item.$1,
              trailing: '${item.$2} ($percentage%)',
              percentage: percentage,
              color: item.$3,
            ),
          );
        }).toList(),
      ),
    );
  }
}

class UpcomingDueCard extends StatelessWidget {
  const UpcomingDueCard(
      {super.key, required this.items, required this.onViewAll});

  final List<UpcomingDueItem> items;
  final VoidCallback onViewAll;

  @override
  Widget build(BuildContext context) {
    return DashboardSectionCard(
      title: 'Tagihan Segera Jatuh Tempo',
      subtitle: 'Prioritas penagihan terdekat',
      action: TextButton(onPressed: onViewAll, child: const Text('Lihat')),
      child: items.isEmpty
          ? const EmptyDashboardText(
              text: 'Tidak ada tagihan mendekati jatuh tempo')
          : Column(
              children: items.map((item) {
                final color = item.days < 0
                    ? const Color(0xFFDC2626)
                    : item.days <= 1
                        ? const Color(0xFFF59E0B)
                        : const Color(0xFF64748B);
                return ListTile(
                  contentPadding: EdgeInsets.zero,
                  leading: CircleAvatar(
                    backgroundColor: color.withValues(alpha: 0.12),
                    child: Text(
                        item.customerName.isEmpty
                            ? '?'
                            : item.customerName[0].toUpperCase(),
                        style: TextStyle(
                            color: color, fontWeight: FontWeight.w900)),
                  ),
                  title: Text(item.customerName,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontWeight: FontWeight.w800)),
                  subtitle: Text(item.packageName,
                      maxLines: 1, overflow: TextOverflow.ellipsis),
                  trailing: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(rupiah(item.amount),
                          style: const TextStyle(
                              fontWeight: FontWeight.w900, fontSize: 12)),
                      Text(item.dueLabel,
                          style: TextStyle(
                              color: color,
                              fontWeight: FontWeight.w800,
                              fontSize: 11)),
                    ],
                  ),
                );
              }).toList(),
            ),
    );
  }
}

class NetworkCard extends StatelessWidget {
  const NetworkCard({super.key, required this.network});

  final NetworkSummary network;

  @override
  Widget build(BuildContext context) {
    return DashboardSectionCard(
      title: 'Jaringan',
      subtitle: '${network.uptimePercentage}% router online',
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                  child: NetworkMiniCard(
                      label: 'Router',
                      value: '${network.totalRouters}',
                      color: const Color(0xFF64748B))),
              const SizedBox(width: 8),
              Expanded(
                  child: NetworkMiniCard(
                      label: 'Online',
                      value: '${network.onlineRouters}',
                      color: const Color(0xFF16A34A))),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                  child: NetworkMiniCard(
                      label: 'PPPoE Aktif',
                      value: '${network.totalPppoe}',
                      color: const Color(0xFF0284C7))),
              const SizedBox(width: 8),
              Expanded(
                  child: NetworkMiniCard(
                      label: 'Mapped',
                      value: '${network.mappedCustomers}',
                      color: const Color(0xFF7C3AED))),
            ],
          ),
          const SizedBox(height: 14),
          MetricBar(
            label: 'Uptime Rate',
            trailing: '${network.uptimePercentage}%',
            percentage: network.uptimePercentage,
            color: const Color(0xFF16A34A),
          ),
        ],
      ),
    );
  }
}

class NetworkMiniCard extends StatelessWidget {
  const NetworkMiniCard(
      {super.key,
      required this.label,
      required this.value,
      required this.color});

  final String label;
  final String value;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.10),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Column(
        children: [
          Text(value,
              style: TextStyle(
                  color: color, fontWeight: FontWeight.w900, fontSize: 20)),
          Text(label,
              style: const TextStyle(color: Color(0xFF64748B), fontSize: 11)),
        ],
      ),
    );
  }
}

class LatestCustomersCard extends StatelessWidget {
  const LatestCustomersCard({super.key, required this.customers});

  final List<LatestCustomerItem> customers;

  @override
  Widget build(BuildContext context) {
    return DashboardSectionCard(
      title: 'Pelanggan Terbaru',
      subtitle: 'Aktivitas registrasi terbaru',
      child: customers.isEmpty
          ? const EmptyDashboardText(text: 'Belum ada pelanggan')
          : Column(
              children: customers.map((customer) {
                final color = customer.status == 'aktif'
                    ? const Color(0xFF16A34A)
                    : customer.status == 'suspend'
                        ? const Color(0xFFF59E0B)
                        : const Color(0xFFDC2626);
                return ListTile(
                  contentPadding: EdgeInsets.zero,
                  leading: CircleAvatar(
                    backgroundColor: const Color(0xFF0284C7),
                    child: Text(
                        customer.name.isEmpty
                            ? '?'
                            : customer.name[0].toUpperCase(),
                        style: const TextStyle(
                            color: Colors.white, fontWeight: FontWeight.w900)),
                  ),
                  title: Text(customer.name,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontWeight: FontWeight.w800)),
                  subtitle: Text(
                      '${customer.packageName} - ${customer.joinDateLabel}',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis),
                  trailing:
                      StatusPill(label: customer.statusLabel, color: color),
                );
              }).toList(),
            ),
    );
  }
}

class DashboardSectionCard extends StatelessWidget {
  const DashboardSectionCard({
    super.key,
    required this.title,
    required this.subtitle,
    required this.child,
    this.action,
  });

  final String title;
  final String subtitle;
  final Widget child;
  final Widget? action;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title,
                        style: const TextStyle(
                            fontWeight: FontWeight.w900,
                            color: Color(0xFF0F172A))),
                    const SizedBox(height: 3),
                    Text(subtitle,
                        style: const TextStyle(
                            color: Color(0xFF94A3B8), fontSize: 12)),
                  ],
                ),
              ),
              if (action != null) action!,
            ],
          ),
          const SizedBox(height: 14),
          child,
        ],
      ),
    );
  }
}

class MetricBar extends StatelessWidget {
  const MetricBar({
    super.key,
    required this.label,
    required this.trailing,
    required this.percentage,
    required this.color,
  });

  final String label;
  final String trailing;
  final int percentage;
  final Color color;

  @override
  Widget build(BuildContext context) {
    final widthFactor = (percentage.clamp(0, 100)) / 100;
    return Column(
      children: [
        Row(
          children: [
            Expanded(
                child: Text(label,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        color: Color(0xFF334155),
                        fontWeight: FontWeight.w700,
                        fontSize: 12))),
            Text(trailing,
                style: const TextStyle(color: Color(0xFF64748B), fontSize: 12)),
          ],
        ),
        const SizedBox(height: 6),
        ClipRRect(
          borderRadius: BorderRadius.circular(999),
          child: LinearProgressIndicator(
            value: widthFactor,
            minHeight: 8,
            backgroundColor: const Color(0xFFE2E8F0),
            valueColor: AlwaysStoppedAnimation<Color>(color),
          ),
        ),
      ],
    );
  }
}

class EmptyDashboardText extends StatelessWidget {
  const EmptyDashboardText({super.key, required this.text});

  final String text;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 20),
      child: Center(
        child: Text(text,
            textAlign: TextAlign.center,
            style: const TextStyle(color: Color(0xFF94A3B8))),
      ),
    );
  }
}

class StaffInvoicesPage extends StatefulWidget {
  const StaffInvoicesPage({
    super.key,
    required this.session,
  });

  final StaffSession session;

  @override
  State<StaffInvoicesPage> createState() => _StaffInvoicesPageState();
}

class _StaffInvoicesPageState extends State<StaffInvoicesPage> {
  final _api = const MobileApi();
  late Future<StaffInvoiceResponse> _future;
  late String _period;
  String _filter = 'all';

  @override
  void initState() {
    super.initState();
    _period = DateFormat('yyyy-MM').format(DateTime.now());
    _future = _api.staffInvoices(widget.session.token, _period);
  }

  void _refresh() {
    setState(() => _future = _api.staffInvoices(widget.session.token, _period));
  }

  void _movePeriod(int delta) {
    final parts = _period.split('-');
    final current = DateTime(int.parse(parts[0]), int.parse(parts[1]));
    final next = DateTime(current.year, current.month + delta);
    setState(() {
      _period = DateFormat('yyyy-MM').format(next);
      _future = _api.staffInvoices(widget.session.token, _period);
    });
  }

  Future<void> _updateStatus(StaffInvoice invoice, String status) async {
    try {
      await _api.updateStaffInvoiceStatus(
          widget.session.token, invoice.id, status);
      _refresh();
      if (mounted) showSnack(context, 'Status tagihan berhasil diperbarui.');
    } on ApiException catch (error) {
      if (mounted) showSnack(context, error.message);
    } catch (_) {
      if (mounted) showSnack(context, 'Tidak bisa terhubung ke server.');
    }
  }

  Future<void> _updatePaymentMethod(StaffInvoice invoice, String method) async {
    try {
      await _api.updateStaffPaymentMethod(
          widget.session.token, invoice.id, method);
      _refresh();
      if (mounted) showSnack(context, 'Metode bayar berhasil diperbarui.');
    } on ApiException catch (error) {
      if (mounted) showSnack(context, error.message);
    } catch (_) {
      if (mounted) showSnack(context, 'Tidak bisa terhubung ke server.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tagihan Staff'),
        centerTitle: false,
        actions: [
          IconButton(
            tooltip: 'Muat ulang',
            onPressed: _refresh,
            icon: const Icon(Icons.refresh_rounded),
          ),
        ],
      ),
      body: FutureBuilder<StaffInvoiceResponse>(
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

          final data = snapshot.data!;
          final invoices = _filter == 'all'
              ? data.invoices
              : data.invoices.where((item) => item.status == _filter).toList();

          return RefreshIndicator(
            onRefresh: () async => _refresh(),
            child: ListView(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
              children: [
                StaffHeaderCard(session: widget.session),
                const SizedBox(height: 12),
                PeriodBar(
                  period: _period,
                  onPrevious: () => _movePeriod(-1),
                  onNext: () => _movePeriod(1),
                ),
                const SizedBox(height: 12),
                StaffStatsRow(stats: data.stats),
                const SizedBox(height: 14),
                StatusFilterChips(
                  selected: _filter,
                  onChanged: (value) => setState(() => _filter = value),
                  stats: data.stats,
                ),
                const SizedBox(height: 14),
                if (invoices.isEmpty)
                  const EmptyInvoices()
                else
                  ...invoices.map(
                    (invoice) => StaffInvoiceTile(
                      invoice: invoice,
                      onStatusChanged: (status) =>
                          _updateStatus(invoice, status),
                      onPaymentMethodChanged: (method) =>
                          _updatePaymentMethod(invoice, method),
                    ),
                  ),
              ],
            ),
          );
        },
      ),
    );
  }
}

class StaffHeaderCard extends StatelessWidget {
  const StaffHeaderCard({super.key, required this.session});

  final StaffSession session;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFF0F172A),
        borderRadius: BorderRadius.circular(24),
      ),
      child: Row(
        children: [
          const IconBubble(
              icon: Icons.admin_panel_settings_outlined,
              color: Color(0xFF38BDF8)),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  session.name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w900,
                      fontSize: 18),
                ),
                const SizedBox(height: 3),
                Text(
                  session.role.toUpperCase(),
                  style: const TextStyle(
                      color: Color(0xFFBAE6FD),
                      fontSize: 12,
                      fontWeight: FontWeight.w800),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class PeriodBar extends StatelessWidget {
  const PeriodBar({
    super.key,
    required this.period,
    required this.onPrevious,
    required this.onNext,
  });

  final String period;
  final VoidCallback onPrevious;
  final VoidCallback onNext;

  @override
  Widget build(BuildContext context) {
    final parts = period.split('-');
    final date = DateTime(int.parse(parts[0]), int.parse(parts[1]));
    final label = formatPeriodLabel(date);

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        children: [
          IconButton(
            onPressed: onPrevious,
            icon: const Icon(Icons.chevron_left_rounded),
            tooltip: 'Periode sebelumnya',
          ),
          Expanded(
            child: Text(
              label,
              textAlign: TextAlign.center,
              style: const TextStyle(
                  fontWeight: FontWeight.w900, color: Color(0xFF0F172A)),
            ),
          ),
          IconButton(
            onPressed: onNext,
            icon: const Icon(Icons.chevron_right_rounded),
            tooltip: 'Periode berikutnya',
          ),
        ],
      ),
    );
  }
}

class StaffStatsRow extends StatelessWidget {
  const StaffStatsRow({super.key, required this.stats});

  final StaffInvoiceStats stats;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
            child: CompactStatCard(
                label: 'Total',
                value: '${stats.total}',
                color: const Color(0xFF0284C7))),
        const SizedBox(width: 8),
        Expanded(
            child: CompactStatCard(
                label: 'Lunas',
                value: '${stats.paid}',
                color: const Color(0xFF16A34A))),
        const SizedBox(width: 8),
        Expanded(
            child: CompactStatCard(
                label: 'Belum',
                value: '${stats.unpaid}',
                color: const Color(0xFFF59E0B))),
        const SizedBox(width: 8),
        Expanded(
            child: CompactStatCard(
                label: 'Tempo',
                value: '${stats.overdue}',
                color: const Color(0xFFDC2626))),
      ],
    );
  }
}

class CompactStatCard extends StatelessWidget {
  const CompactStatCard({
    super.key,
    required this.label,
    required this.value,
    required this.color,
  });

  final String label;
  final String value;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 78,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(value,
              style: TextStyle(
                  color: color, fontSize: 20, fontWeight: FontWeight.w900)),
          const Spacer(),
          Text(label,
              style: const TextStyle(color: Color(0xFF64748B), fontSize: 11)),
        ],
      ),
    );
  }
}

class StatusFilterChips extends StatelessWidget {
  const StatusFilterChips({
    super.key,
    required this.selected,
    required this.onChanged,
    required this.stats,
  });

  final String selected;
  final ValueChanged<String> onChanged;
  final StaffInvoiceStats stats;

  @override
  Widget build(BuildContext context) {
    final items = [
      ('all', 'Semua', stats.total),
      ('unpaid', 'Belum Dibayar', stats.unpaid),
      ('paid', 'Lunas', stats.paid),
      ('overdue', 'Jatuh Tempo', stats.overdue),
      ('cancelled', 'Batal', stats.cancelled),
    ];

    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: items.map((item) {
          final isSelected = selected == item.$1;
          return Padding(
            padding: const EdgeInsets.only(right: 8),
            child: ChoiceChip(
              selected: isSelected,
              label: Text('${item.$2} ${item.$3}'),
              onSelected: (_) => onChanged(item.$1),
            ),
          );
        }).toList(),
      ),
    );
  }
}

class StaffInvoiceTile extends StatelessWidget {
  const StaffInvoiceTile({
    super.key,
    required this.invoice,
    required this.onStatusChanged,
    required this.onPaymentMethodChanged,
  });

  final StaffInvoice invoice;
  final ValueChanged<String> onStatusChanged;
  final ValueChanged<String> onPaymentMethodChanged;

  @override
  Widget build(BuildContext context) {
    final statusColor = statusColorFor(invoice.status);
    final paymentValue = paymentMethods.contains(invoice.paymentMethod)
        ? invoice.paymentMethod
        : paymentMethods.first;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
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
                    Text(invoice.invoiceNumber,
                        style: const TextStyle(
                            fontWeight: FontWeight.w900,
                            color: Color(0xFF0F172A))),
                    const SizedBox(height: 3),
                    Text(
                      '${invoice.customerName} (${invoice.customerNumber})',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: Color(0xFF64748B)),
                    ),
                  ],
                ),
              ),
              StatusPill(label: invoice.statusLabel, color: statusColor),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: Text(
                  rupiah(invoice.amount),
                  style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w900,
                      color: Color(0xFF111827)),
                ),
              ),
              Text(invoice.billingPeriodLabel,
                  style:
                      const TextStyle(color: Color(0xFF64748B), fontSize: 12)),
            ],
          ),
          const SizedBox(height: 12),
          DropdownButtonFormField<String>(
            initialValue: invoice.status,
            decoration: const InputDecoration(
              labelText: 'Status Pembayaran',
              prefixIcon: Icon(Icons.verified_outlined),
            ),
            items: const [
              DropdownMenuItem(value: 'unpaid', child: Text('Belum Dibayar')),
              DropdownMenuItem(value: 'paid', child: Text('Lunas')),
              DropdownMenuItem(value: 'overdue', child: Text('Jatuh Tempo')),
              DropdownMenuItem(value: 'cancelled', child: Text('Dibatalkan')),
            ],
            onChanged: (value) {
              if (value != null && value != invoice.status) {
                onStatusChanged(value);
              }
            },
          ),
          const SizedBox(height: 10),
          DropdownButtonFormField<String>(
            initialValue: paymentValue,
            decoration: const InputDecoration(
              labelText: 'Metode Bayar',
              prefixIcon: Icon(Icons.payments_outlined),
            ),
            items: paymentMethods
                .map((method) =>
                    DropdownMenuItem(value: method, child: Text(method)))
                .toList(),
            onChanged: (value) {
              if (value != null && value != invoice.paymentMethod) {
                onPaymentMethodChanged(value);
              }
            },
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              const Icon(Icons.schedule_outlined,
                  size: 16, color: Color(0xFF94A3B8)),
              const SizedBox(width: 6),
              Expanded(
                child: Text('Jatuh tempo ${invoice.dueDateLabel}',
                    style: const TextStyle(
                        color: Color(0xFF64748B), fontSize: 12)),
              ),
              TextButton.icon(
                onPressed: invoice.pdfUrl.isEmpty
                    ? null
                    : () => openExternalUrl(context, invoice.pdfUrl),
                icon: const Icon(Icons.picture_as_pdf_outlined),
                label: const Text('PDF'),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

const paymentMethods = ['Transfer Bank', 'Tunai', 'QRIS', 'E-Wallet'];

class CustomerDashboardPage extends StatefulWidget {
  const CustomerDashboardPage({
    super.key,
    required this.customerNumber,
    required this.onLogout,
  });

  final String customerNumber;
  final VoidCallback onLogout;

  @override
  State<CustomerDashboardPage> createState() => _CustomerDashboardPageState();
}

class _CustomerDashboardPageState extends State<CustomerDashboardPage> {
  final _api = const MobileApi();
  late Future<DashboardData> _future;

  @override
  void initState() {
    super.initState();
    _future = _load();
  }

  Future<DashboardData> _load() async {
    final results = await Future.wait([
      _api.invoices(widget.customerNumber),
      _api.news(),
    ]);

    return DashboardData(
      session: results[0] as CustomerSession,
      news: results[1] as List<NewsItem>,
    );
  }

  void _refresh() {
    setState(() => _future = _load());
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
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
      body: FutureBuilder<DashboardData>(
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

          final data = snapshot.data!;
          return RefreshIndicator(
            onRefresh: () async => _refresh(),
            child: ListView(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
              children: [
                WelcomeDashboardCard(session: data.session),
                const SizedBox(height: 14),
                ServiceInfoGrid(session: data.session),
                const SizedBox(height: 20),
                const SectionTitle(title: 'Informasi Terbaru'),
                const SizedBox(height: 10),
                if (data.news.isEmpty)
                  const EmptyNews()
                else
                  ...data.news.map((item) => NewsTile(item: item)),
                const SizedBox(height: 18),
                BillingShortcutCard(
                  session: data.session,
                  onTap: () {
                    Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => BillingPage(
                          initialSession: data.session,
                          customerNumber: widget.customerNumber,
                        ),
                      ),
                    );
                  },
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}

class WelcomeDashboardCard extends StatelessWidget {
  const WelcomeDashboardCard({super.key, required this.session});

  final CustomerSession session;

  @override
  Widget build(BuildContext context) {
    final customer = session.customer;
    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: const Color(0xFF0369A1),
        borderRadius: BorderRadius.circular(26),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Text(
                  'Halo, ${customer.firstName}!',
                  style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                        color: Colors.white,
                        fontWeight: FontWeight.w900,
                      ),
                ),
              ),
              const Icon(Icons.waving_hand_rounded, color: Color(0xFFFBBF24)),
            ],
          ),
          const SizedBox(height: 8),
          const Text(
            'Selamat datang di Portal Pelanggan Tim-7 Net. Pantau layanan, baca pengumuman, dan cek tagihan dari satu dashboard.',
            style: TextStyle(color: Color(0xFFE0F2FE), height: 1.5),
          ),
          const SizedBox(height: 18),
          Row(
            children: [
              Expanded(
                child: MiniMetric(
                  label: 'Tagihan aktif',
                  value: '${session.summary.dueCount}',
                  icon: Icons.receipt_long_outlined,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: MiniMetric(
                  label: 'Outstanding',
                  value: rupiah(session.summary.outstandingAmount),
                  icon: Icons.account_balance_wallet_outlined,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class MiniMetric extends StatelessWidget {
  const MiniMetric({
    super.key,
    required this.label,
    required this.value,
    required this.icon,
  });

  final String label;
  final String value;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: const Color(0xFFBAE6FD), size: 20),
          const SizedBox(height: 8),
          Text(
            label,
            style: const TextStyle(color: Color(0xFFBAE6FD), fontSize: 11),
          ),
          const SizedBox(height: 3),
          FittedBox(
            fit: BoxFit.scaleDown,
            alignment: Alignment.centerLeft,
            child: Text(
              value,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 18,
                fontWeight: FontWeight.w900,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class ServiceInfoGrid extends StatelessWidget {
  const ServiceInfoGrid({super.key, required this.session});

  final CustomerSession session;

  @override
  Widget build(BuildContext context) {
    final customer = session.customer;
    final isActive = customer.status.toLowerCase() == 'aktif';
    return GridView.count(
      crossAxisCount: 2,
      childAspectRatio: 1.7,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 10,
      crossAxisSpacing: 10,
      children: [
        DashboardInfoCard(
          label: 'Status',
          value: customer.statusLabel,
          icon: Icons.wifi_tethering_rounded,
          color: isActive ? const Color(0xFF16A34A) : const Color(0xFFDC2626),
        ),
        DashboardInfoCard(
          label: 'ID Pelanggan',
          value: customer.customerNumber,
          icon: Icons.badge_outlined,
          color: const Color(0xFF0284C7),
        ),
        DashboardInfoCard(
          label: 'Paket',
          value: customer.packageName,
          icon: Icons.router_outlined,
          color: const Color(0xFF7C3AED),
        ),
        DashboardInfoCard(
          label: 'Jadwal Tagihan',
          value: 'Tgl ${customer.billingDate}',
          icon: Icons.event_available_outlined,
          color: const Color(0xFFF97316),
        ),
      ],
    );
  }
}

class DashboardInfoCard extends StatelessWidget {
  const DashboardInfoCard({
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
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 22),
          const Spacer(),
          Text(
            label,
            style: const TextStyle(color: Color(0xFF64748B), fontSize: 11),
          ),
          const SizedBox(height: 2),
          Text(
            value,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(
              color: Color(0xFF0F172A),
              fontWeight: FontWeight.w900,
            ),
          ),
        ],
      ),
    );
  }
}

class NewsTile extends StatelessWidget {
  const NewsTile({super.key, required this.item});

  final NewsItem item;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const IconBubble(
            icon: Icons.campaign_outlined,
            color: Color(0xFF0284C7),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Flexible(
                      child: Text(
                        item.categoryLabel,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          color: Color(0xFF0284C7),
                          fontWeight: FontWeight.w800,
                          fontSize: 11,
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Text(
                      item.publishedAtLabel,
                      style: const TextStyle(
                        color: Color(0xFF94A3B8),
                        fontSize: 11,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 5),
                Text(
                  item.title,
                  style: const TextStyle(
                    color: Color(0xFF0F172A),
                    fontWeight: FontWeight.w900,
                  ),
                ),
                if (item.excerpt.isNotEmpty) ...[
                  const SizedBox(height: 5),
                  Text(
                    item.excerpt,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      color: Color(0xFF64748B),
                      height: 1.35,
                    ),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class EmptyNews extends StatelessWidget {
  const EmptyNews({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: const Row(
        children: [
          IconBubble(icon: Icons.newspaper_outlined, color: Color(0xFF94A3B8)),
          SizedBox(width: 12),
          Expanded(
            child: Text(
              'Belum ada berita terbaru. Informasi layanan akan muncul di sini saat tersedia.',
              style: TextStyle(color: Color(0xFF64748B)),
            ),
          ),
        ],
      ),
    );
  }
}

class BillingShortcutCard extends StatelessWidget {
  const BillingShortcutCard({
    super.key,
    required this.session,
    required this.onTap,
  });

  final CustomerSession session;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const IconBubble(
                icon: Icons.receipt_long_outlined,
                color: Color(0xFFF97316),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Tagihan Pelanggan',
                      style: TextStyle(
                        fontWeight: FontWeight.w900,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                    Text(
                      '${session.summary.dueCount} tagihan perlu perhatian',
                      style: const TextStyle(color: Color(0xFF64748B)),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          FilledButton.icon(
            onPressed: onTap,
            icon: const Icon(Icons.arrow_forward_rounded),
            label: const Text('Lihat Tagihan'),
          ),
        ],
      ),
    );
  }
}

class BillingPage extends StatefulWidget {
  const BillingPage({
    super.key,
    required this.customerNumber,
    this.initialSession,
  });

  final String customerNumber;
  final CustomerSession? initialSession;

  @override
  State<BillingPage> createState() => _BillingPageState();
}

class _BillingPageState extends State<BillingPage> {
  final _api = const MobileApi();
  late Future<CustomerSession> _future;

  @override
  void initState() {
    super.initState();
    _future = widget.initialSession != null
        ? Future.value(widget.initialSession)
        : _api.invoices(widget.customerNumber);
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
                const SectionTitle(title: 'Riwayat Tagihan'),
                const SizedBox(height: 10),
                if (session.invoices.isEmpty)
                  const EmptyInvoices()
                else
                  ...session.invoices.map(
                    (invoice) => InvoiceTile(invoice: invoice),
                  ),
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
                color: isActive
                    ? const Color(0xFF16A34A)
                    : const Color(0xFFDC2626),
                light: true,
              ),
            ],
          ),
          const SizedBox(height: 16),
          InfoLine(
            icon: Icons.confirmation_number_outlined,
            label: 'ID',
            value: customer.customerNumber,
          ),
          InfoLine(
            icon: Icons.router_outlined,
            label: 'Paket',
            value: customer.packageName,
          ),
          InfoLine(
            icon: Icons.event_available_outlined,
            label: 'Tagihan',
            value: 'Setiap tanggal ${customer.billingDate}',
          ),
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
          Text(
            '$label: ',
            style: const TextStyle(
              color: Color(0xFFE0F2FE),
              fontWeight: FontWeight.w600,
            ),
          ),
          Expanded(
            child: Text(
              value,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w700,
              ),
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
            value: '${summary.dueCount}',
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
          Text(
            label,
            style: const TextStyle(color: Color(0xFF64748B), fontSize: 12),
          ),
          const SizedBox(height: 3),
          FittedBox(
            alignment: Alignment.centerLeft,
            fit: BoxFit.scaleDown,
            child: Text(
              value,
              style: const TextStyle(
                fontWeight: FontWeight.w900,
                fontSize: 20,
                color: Color(0xFF0F172A),
              ),
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
                    Text(
                      invoice.billingPeriodLabel,
                      style: const TextStyle(color: Color(0xFF64748B)),
                    ),
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
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                    color: Color(0xFF111827),
                  ),
                ),
              ),
              const Icon(
                Icons.schedule_outlined,
                size: 16,
                color: Color(0xFF94A3B8),
              ),
              const SizedBox(width: 5),
              Text(
                invoice.dueDateLabel,
                style: const TextStyle(color: Color(0xFF64748B), fontSize: 12),
              ),
            ],
          ),
          if ((invoice.paymentMethod ?? '').isNotEmpty) ...[
            const SizedBox(height: 10),
            Text(
              'Metode: ${invoice.paymentMethod}',
              style: const TextStyle(color: Color(0xFF64748B), fontSize: 12),
            ),
          ],
          const SizedBox(height: 10),
          Align(
            alignment: Alignment.centerRight,
            child: TextButton.icon(
              onPressed: invoice.pdfUrl.isEmpty
                  ? null
                  : () => openExternalUrl(context, invoice.pdfUrl),
              icon: const Icon(Icons.picture_as_pdf_outlined),
              label: const Text('Cetak PDF'),
            ),
          ),
        ],
      ),
    );
  }
}

class BrandMark extends StatelessWidget {
  const BrandMark({super.key, required this.size});

  final double size;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(size * 0.26),
        boxShadow: const [
          BoxShadow(
            color: Color(0x1A0F172A),
            blurRadius: 24,
            offset: Offset(0, 12),
          ),
        ],
      ),
      child: Icon(
        Icons.wifi_rounded,
        size: size * 0.56,
        color: const Color(0xFF0284C7),
      ),
    );
  }
}

class IconBubble extends StatelessWidget {
  const IconBubble({super.key, required this.icon, required this.color});

  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 42,
      height: 42,
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Icon(icon, color: color, size: 22),
    );
  }
}

class SectionTitle extends StatelessWidget {
  const SectionTitle({super.key, required this.title});

  final String title;

  @override
  Widget build(BuildContext context) {
    return Text(
      title,
      style: Theme.of(context).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.w900,
            color: const Color(0xFF111827),
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
        color: light
            ? Colors.white.withValues(alpha: 0.18)
            : color.withValues(alpha: 0.12),
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
          Text(
            'Belum ada riwayat tagihan.',
            style: TextStyle(color: Color(0xFF64748B)),
          ),
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
          const Icon(
            Icons.error_outline_rounded,
            color: Color(0xFFDC2626),
            size: 18,
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              message,
              style: const TextStyle(color: Color(0xFFB91C1C)),
            ),
          ),
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

int intValue(Object? value) => int.tryParse(value?.toString() ?? '') ?? 0;

String rupiahCompact(int amount) {
  if (amount >= 1000000) {
    final value = amount / 1000000;
    return 'Rp ${value.toStringAsFixed(1).replaceAll('.', ',')} Jt';
  }

  return rupiah(amount);
}

String formatPeriodLabel(DateTime date) {
  const months = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
  ];

  return '${months[date.month - 1]} ${date.year}';
}

Color statusColorFor(String status) {
  return switch (status) {
    'paid' => const Color(0xFF16A34A),
    'overdue' => const Color(0xFFDC2626),
    'cancelled' => const Color(0xFF64748B),
    _ => const Color(0xFFF59E0B),
  };
}

Future<void> openExternalUrl(BuildContext context, String url) async {
  final uri = Uri.tryParse(url);
  if (uri == null) {
    showSnack(context, 'URL PDF tidak valid.');
    return;
  }

  final opened = await launchUrl(uri, mode: LaunchMode.externalApplication);
  if (!opened && context.mounted) {
    showSnack(context, 'Tidak bisa membuka PDF.');
  }
}

void showSnack(BuildContext context, String message) {
  ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(message)));
}
