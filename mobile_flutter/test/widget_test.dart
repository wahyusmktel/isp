import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'package:tim7_net_mobile/main.dart';

void main() {
  testWidgets('shows landing page before customer login',
      (WidgetTester tester) async {
    SharedPreferences.setMockInitialValues({});

    await tester.pumpWidget(const Tim7NetApp());
    await tester.pump(const Duration(milliseconds: 200));

    expect(find.text('Tim-7 Net'), findsOneWidget);
    expect(find.text('Internet cepat, stabil, dan siap menemani rumah Anda.'),
        findsOneWidget);
    expect(find.text('Masuk Portal Pelanggan'), findsOneWidget);
  });

  testWidgets('opens customer login from landing page',
      (WidgetTester tester) async {
    SharedPreferences.setMockInitialValues({});

    await tester.pumpWidget(const Tim7NetApp());
    await tester.pump(const Duration(milliseconds: 200));

    await tester.tap(find.text('Masuk Portal Pelanggan'));
    await tester.pumpAndSettle();

    expect(find.text('Portal Pelanggan'), findsOneWidget);
    expect(find.text('Nomor Pelanggan'), findsOneWidget);
    expect(find.text('Masuk Dashboard'), findsOneWidget);
  });
}
