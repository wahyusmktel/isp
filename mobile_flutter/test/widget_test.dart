import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'package:tim7_net_mobile/main.dart';

void main() {
  testWidgets('shows customer login page', (WidgetTester tester) async {
    SharedPreferences.setMockInitialValues({});

    await tester.pumpWidget(const Tim7NetApp());
    await tester.pump(const Duration(milliseconds: 200));

    expect(find.text('Portal Pelanggan'), findsOneWidget);
    expect(find.text('Nomor Pelanggan'), findsOneWidget);
    expect(find.text('Masuk'), findsOneWidget);
  });
}
