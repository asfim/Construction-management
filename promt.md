# Bangladesh Construction Management ERP — A to Z Master Plan & Development Prompts

**Prepared for:** H Tech Provision IT  
**Market:** Bangladesh construction contractors, developers, engineering firms and multi-project builders  
**Target stack:** Laravel 12, PHP 8.3+, MySQL 8+, Bootstrap 5.3, Blade, JavaScript, Redis/queue optional  
**Language:** Bangla default, English switch  
**Currency:** BDT (৳), Asia/Dhaka timezone, DD-MM-YYYY display

> This is a product specification and AI development prompt, not a claim that the software is already implemented. Tax, labor, and compliance logic must be configurable and reviewed against current Bangladesh law by qualified professionals before production.

## 1. Business goals and supported organizations
- Contractor: tender → estimate → contract → execution → billing → retention → handover.
- Real-estate developer: land/project → units → customer booking → installment → handover.
- Engineering consultancy: design, drawing revisions, inspections, fees.
- Subcontractor: work order → labor/material → measurement → bill.
- Multi-company, multi-branch, multi-project support; isolate data by company and project.

## 2. User roles and access control
Super Admin, Company Owner, Managing Director, Project Director, Project Manager, Site Engineer, Planning Engineer, Quantity Surveyor (QS), Estimator, Procurement Manager, Storekeeper, Accounts Manager, Accountant, HR Manager, Safety Officer, Quality Inspector, Equipment Manager, Sales Manager, CRM Executive, Contractor/Subcontractor, Supplier Portal User, Client Portal User, Auditor, Viewer.

- RBAC with permissions per module/action (view, create, edit, approve, delete, export).
- Scope by company, branch, project, warehouse, and assigned site.
- Maker-checker workflow, delegated approvals, 2FA optional, audit trail, session history.

## 3. Full module inventory

### A. Organization & system settings
Company profile, BIN/TIN fields, trade license, fiscal year, branches, projects, currency, units (sft, sqft, cft, sqm, m3, kg, ton, bag, rod), holidays, Bangla/English labels, document numbering, custom fields, notification templates, backup and data retention.

### B. Lead, CRM & business development
Prospects, site visits, lead sources, client/company contacts, follow-ups, quotation, pipeline, activity logs, WhatsApp/SMS/email opt-in, proposal attachments, win/loss analysis.

### C. Tender / bid management
Tender notice, tender authority, eligibility checklist, document purchase, bid security, BOQ import, estimation, bid comparison, submission deadlines, tender status, award, performance security, contract creation, variation history.

### D. Project master & lifecycle
Project code, owner, location, budget, funding, contract value, start/end dates, site teams, status, phases, milestones, baseline vs actual, project dashboard, archived projects.

### E. WBS, planning & scheduling
Work breakdown structure, activities, dependencies, critical path (optional), Gantt chart, resource assignment, lookahead plan, baseline snapshots, delay reasons, extension of time (EOT), progress % and earned-value indicators.

### F. BOQ, estimation & rate analysis
BOQ sections/items, unit, planned quantity, rate, amount, labor/material/equipment rate breakdown, overhead, profit, contingency, wastage, supplier rate history, revision/version, approved baseline, Excel import/export. Do not overwrite historical approved versions.

### G. Budget, cost code & cost control
Cost codes, original budget, approved revisions, committed cost (PO/work order), actual cost, forecast-to-complete, estimate-at-completion, cost variance, budget alerts, cash-flow forecast, project profitability.

### H. Design, drawing & document control
Drawing register, disciplines, revisions, approval status, RFI, submittals, transmittals, technical queries, document versions, access permissions, signed files, revision acknowledgments.

### I. Procurement & purchasing
Material requisition (MR), approval, RFQ, supplier quotations, comparison sheet (CS), purchase requisition, PO, delivery schedule, partial receipts, supplier invoice, purchase returns, advance, supplier ledger, VAT/tax fields.

### J. Inventory, warehouse & materials
Multiple warehouses/site stores, item SKU, category, brand, specification, batch, unit conversion, minimum stock, opening stock, GRN, issue, return, transfer, adjustments, physical count, stock valuation, damaged/wasted material, lot traceability, stock ledger.

### K. Site execution & daily progress
Daily progress report (DPR), daily work quantities, labor headcount, material consumption, equipment usage, weather, site photos, site instructions, blockers, geo-tag optional with consent, supervisor approvals, offline draft sync optional.

### L. Measurement, billing & contracts
Client contract, subcontract agreements, measurement book (MB), running account (RA) bills, interim payment certificates (IPC), final bill, variations/change orders, escalation adjustments, advance recovery, retention, security deposit, deductions, certified vs paid amounts, receivables/payables.

### M. Subcontractor management
Registration, qualification, contract, scope/BOQ, assigned tasks, site progress, measurement, invoices, retention, penalties (contract-configured), payments, performance scorecard, disputes.

### N. Labor, HR & attendance
Employee records, labor groups, daily/casual workers, skill/trade, shifts, attendance, overtime rules, leave, holidays, wages, advances, payroll, attendance device integration (optional ZKTeco via approved device sync adapter), compliance document storage.

### O. Equipment, vehicle & fuel
Asset register, ownership/rental, assignment, usage hours, fuel log, operator, maintenance, breakdown, spare parts, rental invoices, depreciation policy, utilization and idle time.

### P. Quality assurance / quality control
Inspection test plan (ITP), checklist, inspection requests, material tests, cube tests, slump tests, NCR, corrective/preventive action, punch list, sign-off, photo evidence, approvals.

### Q. HSE / safety
Induction, toolbox talk, PPE issue, permits to work, hazard assessment, incident/near-miss reports, corrective action, site safety audit, emergency contacts, safety dashboard.

### R. Finance & accounting
Chart of accounts, journal entries, double-entry general ledger, cost centers, cash/bank, vouchers, petty cash, bank reconciliation, AP/AR, receipts, payments, project advances, fixed assets, P&L, balance sheet, cash flow, project-wise financial reports. Posted entries must be reversed, not silently edited.

### S. Bangladesh tax and statutory configuration
Configurable VAT rates, VDS/TDS rules by transaction/date/counterparty, BIN/TIN, invoice templates, withholding certificates, tax registers, NBR-compatible export fields where applicable. **Never hard-code statutory percentages or assert universal applicability**; accountant approval and legal review required. Optional e-GP/tender references without assuming government API availability.

### T. Real-estate developer extension (optional)
Landowners, joint-venture agreements, project blocks, floors, apartments/shops/plots, unit availability, pricing, bookings, customer KYC, installment schedule, collections, cancellation, registration/transfer document tracker, handover, defects-liability service requests. Keep this optional from core contractor ERP.

### U. Customer and supplier portals
Restricted portal access to relevant documents, project milestones, certified bills, payment status, support tickets, approvals and communication history; no cross-project leakage.

### V. Notifications & automation
In-app, email, optional SMS and WhatsApp through approved providers; MR/PO approval, low stock, overdue bills, milestone delay, contract expiry, equipment service due, attendance anomalies. Queue with retries, rate limits, templates, opt-in where necessary.

### W. Dashboards, analytics & reporting
Executive portfolio KPI; project budget vs actual; cash-flow forecast; procurement aging; material consumption vs BOQ; stock movement; labor productivity; equipment cost; site progress; subcontractor payable; receivable aging; VAT/TDS registers; quality/safety; export PDF/Excel/CSV; scheduled reports.

### X. Mobile/PWA and integrations
Responsive site engineer PWA, photo upload/compression, low-connectivity drafts, barcode/QR inventory, spreadsheet imports, optional maps, payment gateway integration after provider approval, SMS gateway, biometric devices through secure connector. No credentials in frontend code.

### Y. Handover, defects & warranty
As-built drawings, completion certificate, snag/punch closeout, manuals, warranties, handover checklist, retention release milestones, defects-liability tracking, archived record access.

### Z. Administration, security & operations
Audit logs, activity feed, approval history, password policies, optional 2FA, least-privilege roles, tenant isolation, encryption of sensitive data, secure file storage, backup/restore drills, disaster recovery, monitoring, performance, retention policies.

## 4. End-to-end workflows

**Contractor:** Lead → Tender → BOQ estimate → Award → Contract → Project/WBS/budget → Material requisition → RFQ/CS → PO → GRN → Stock issue → Daily progress → MB → RA bill → Client certification → Receipt → Final bill → Handover.

**Subcontractor:** Prequalification → Work order → Daily work → MB verification → Bill → Deductions/retention → Approval → Payment → Closeout.

**Developer:** Land/project → Unit inventory → Lead → Booking → Agreement → Installments → Receipts → Handover → Defect support.

**Controls:** Approved PO cannot exceed budget without override; stock cannot go negative unless explicit company policy; duplicate supplier invoice flagged; accounting entries balanced; approval cannot be self-approved when maker-checker applies; every variation has a traceable contract impact.

## 5. Suggested relational database architecture

**Tenant/identity:** companies, branches, users, company_user, roles, permissions, model_has_roles, projects, project_members, settings, audit_logs, approval_workflows, approval_steps, approval_requests, notifications.

**CRM/tender:** clients, client_contacts, leads, lead_activities, quotations, tenders, tender_documents, tender_boq_items, tender_bids, contracts, contract_documents.

**Planning/BOQ:** project_phases, wbs_items, tasks, task_dependencies, milestones, schedules, schedule_baselines, boq_versions, boq_items, rate_analyses, rate_analysis_components, cost_codes, budgets, budget_lines, budget_revisions, forecasts.

**Procurement:** suppliers, supplier_contacts, material_requisitions, mr_items, rfqs, rfq_suppliers, supplier_quotations, quotation_items, bid_comparisons, purchase_orders, po_items, goods_receipts, grn_items, purchase_returns, supplier_invoices.

**Inventory:** items, item_categories, units, unit_conversions, warehouses, stock_locations, inventory_transactions, inventory_transaction_lines, stock_counts, stock_adjustments, stock_transfers, stock_issues, stock_issue_items.

**Execution:** daily_progress_reports, dpr_activities, dpr_labor, dpr_materials, dpr_equipment, site_photos, site_instructions, measurements, measurement_lines, variations, change_orders, inspections.

**Billing:** client_bills, client_bill_lines, bill_certifications, subcontractors, subcontracts, subcontract_bills, subcontract_bill_lines, retention_entries, payment_allocations.

**HR/assets:** employees, trades, shifts, attendance_logs, timesheets, leaves, payroll_runs, payroll_lines, equipment, equipment_assignments, fuel_logs, maintenance_jobs, equipment_usage.

**QA/HSE/docs:** drawings, drawing_revisions, rfis, submittals, transmittals, quality_checklists, quality_inspections, test_results, nonconformances, corrective_actions, incidents, toolbox_talks, safety_inspections, document_files.

**Accounting:** fiscal_years, accounts, journal_entries, journal_lines, vouchers, cash_accounts, bank_accounts, bank_transactions, reconciliations, receivables, payables, tax_codes, tax_rules, withholding_entries.

**Developer extension:** land_parcels, joint_ventures, buildings, floors, property_units, unit_bookings, unit_customers, installment_plans, installments, unit_payments, unit_handovers.

**Important keys and indexes:** Most business tables carry `company_id`; project-scoped tables also carry `project_id`. Use foreign keys, indexes on (`company_id`,`project_id`,`status`), unique scoped document numbers, `created_by`, `updated_by`, timestamps and soft deletes only where legally/accountingly appropriate. Store money in `DECIMAL(18,2)`, quantity in `DECIMAL(18,4)`; avoid floating-point for accounting. Do not cascade-delete posted financial records.

## 6. Database relationship examples
- Company 1:N Projects; Project 1:N WBS Items; WBS Item 1:N Tasks.
- Project 1:N BOQ Versions; BOQ Version 1:N BOQ Items.
- MR 1:N MR Items; PO 1:N PO Items; PO 1:N GRNs; GRN 1:N GRN Items.
- Warehouse 1:N Inventory Transactions; Item 1:N Inventory Transaction Lines.
- Contract 1:N Client Bills; Client Bill 1:N Bill Lines; Bill 1:N Certifications.
- Journal Entry 1:N Journal Lines; sum(debit) = sum(credit).
- Property Unit 1:N Installments; Unit Booking 1:N Receipts (optional developer module).

## 7. Proposed menu and dashboard UX
**Sidebar:** Dashboard; CRM & Leads; Tenders; Projects; Planning & Gantt; BOQ & Estimation; Budget & Cost; Procurement; Inventory; Site Reports; Measurement & Billing; Contracts & Subcontractors; HR & Payroll; Equipment; Quality; Safety; Finance & Accounts; Documents & Drawings; Reports; Portals; Settings.

**Dashboard cards:** Active Projects, Contract Value, Approved Budget, Committed Cost, Actual Cost, Billed, Collected, Outstanding, Stock Value, Pending Approvals, Overdue Milestones, Incidents. Add project filter, date filter, budget variance chart, cash flow, alerts, recent transactions. Avoid mixing gross and net amounts without labels.

**UI guidelines:** Premium clean Bootstrap 5.3 admin, mobile-first, sidebar + topbar, Bangla typography, accessible forms, data tables with server pagination, search/filter/export, clear approval badges, confirm destructive actions, consistent BDT display, print-ready A4 invoice/PO/MB/RA bill.

## 8. Laravel 12 architecture
- Laravel 12 / PHP 8.3+ / MySQL 8+; Blade + Bootstrap 5.3 + Alpine.js or vanilla JS; use Livewire only if team agrees.
- Separate domains: `App/Modules/{Module}/Controllers`, `Services`, `Actions`, `Requests`, `Policies`, `Models`, `Events`, `Jobs`; or conventional Laravel folders with explicit service classes.
- Use Spatie Laravel Permission (compatible release), policies, Form Requests, database transactions, events, queues, scheduled jobs, notification channels.
- Store uploads privately; authorize signed downloads. Validate MIME/size; malware scanning where feasible.
- Use tenant-scoped query patterns and tests. Do not trust `company_id` or `project_id` from browser input.
- Immutable approved document revisions and immutable posted accounting records.
- Accounting: double-entry postings only in DB transactions with idempotency keys.
- API: versioned `/api/v1`, Sanctum if mobile/API needed, rate limits, pagination.
- Seed realistic Bangladesh sample company, projects, users, suppliers and items; never seed live secrets.

## 9. Bangladesh localization and compliance checklist
- Bangla default; English translations for UI and printable documents.
- BDT formatting; configurable number/date styles; Dhaka timezone.
- Address hierarchy division → district → upazila → area; do not force it for every site.
- Local materials and units: rod, cement, sand, stone, brick, aggregate, sft/cft, ton, bag; configurable conversion.
- Mobile number validation supports +880 and domestic format; don't assume every contact has a mobile.
- Supplier/client TIN, BIN, bank and mobile financial service fields as optional/configurable.
- VAT/VDS/TDS: date-effective configurable rules; export and review by accountant.
- Labor wage, overtime, leave, workplace safety, statutory filings and record retention: confirm applicable rules per employer and contract.
- e-GP tender references may be stored; integrate only when authorized official interface exists.
- Optional Bangla SMS with Unicode-aware segmentation and cost tracking.

## 10. Business rules and sample calculations
- `BOQ line amount = approved quantity × approved rate`.
- `Committed cost = approved open PO + subcontract commitments` (define treatment of received/invoiced amounts to avoid double counting).
- `Actual cost = posted project expense / inventory consumption / labor / equipment allocation`, not merely purchase orders.
- `Budget variance = approved revised budget − forecast final cost`.
- `Bill net payable = certified amount + approved variations − advance recovery − retention − applicable deductions/taxes`.
- `Retention balance = withheld − released`, tracked by contract and bill.
- `Inventory closing quantity = opening + receipts + returns-in − issues − returns-out ± adjustments`.
- `Project gross margin = recognized project revenue − recognized project cost`; recognition policy configurable and accountant-reviewed.

## 11. Approval matrices (configurable)
- MR: Site Engineer → Project Manager → Procurement.
- PO: Procurement → Project Manager → Finance → Director if threshold exceeded.
- GRN: Storekeeper → QA/QC (if required) → Accounts match.
- MB/RA Bill: QS → Project Manager → Client certifier.
- Payment: Accountant prepares → Finance Manager reviews → Authorized approver releases.
- Budget variation: PM → Director → Finance.

## 12. Reports and exports
Daily progress, weekly lookahead, monthly portfolio, BOQ consumption, project cost sheet, budget variance, PO commitment, supplier comparison, procurement aging, stock ledger, material reconciliation, wastage, labor attendance, payroll register, equipment utilization, fuel variance, MB register, RA bill register, retention register, AP/AR aging, cash flow, general ledger, trial balance, P&L, balance sheet, project profitability, safety incidents, quality NCR, tax withholding summary.

## 13. Delivery phases
**Phase 0 — Discovery:** stakeholder interviews, sample forms (PO/GRN/MB/RA bill), workflow maps, approval matrix, tax/accounting review, UX wireframes.

**Phase 1 — MVP:** authentication/RBAC, company/project, BOQ/budget, supplier/item master, MR/PO/GRN, stock, DPR, expense, basic client billing, dashboard/reports, audit trail.

**Phase 2 — Operations:** tenders, WBS/Gantt, full procurement comparison, subcontract, MB/RA billing, equipment, HR/payroll, documents, notifications.

**Phase 3 — Enterprise:** full double-entry accounting, configurable VAT/TDS/VDS, advanced forecasting, QA/HSE, portals, PWA, integrations, multi-company.

**Phase 4 — Developer extension:** unit inventory, bookings, installments, handover.

Deliver every phase with tests, user acceptance criteria, backup/restore instructions, sample data and training.

## 14. Acceptance criteria and QA tests
1. A user from Company A cannot read/write Company B data by changing URL or API IDs.
2. A site engineer cannot approve their own PO if separation of duties is enabled.
3. PO partial delivery produces correct received/pending quantities.
4. GRN and issue create auditable stock transactions; no unapproved negative stock.
5. Approved BOQ revision does not rewrite earlier certified measurements.
6. Every posted journal balances exactly; reversal preserves history.
7. Bill retention, advance recovery, tax and net payable reconcile.
8. Duplicate requests do not create duplicate payment/journal/stock entries.
9. File access enforces project membership and document permissions.
10. Bangla UI, mobile forms, PDF printing, import validation, large-table pagination work.
11. Database restore is tested and backups are encrypted/access-controlled.
12. Date-effective tax configuration is verified by Bangladesh tax professional.

## 15. MASTER AI DEVELOPMENT PROMPT (copy and paste)

```text
You are a principal Laravel architect, senior construction ERP product analyst, database engineer, QA lead and Bootstrap UI designer.

Build a production-oriented Bangladesh Construction Management ERP for H Tech Provision IT using Laravel 12, PHP 8.3+, MySQL 8+, Bootstrap 5.3, Blade, JavaScript, Spatie Permission and secure Laravel conventions.

Product requirements:
- Bangla default, English switch, BDT, Asia/Dhaka, mobile-first design.
- Support multi-company, multi-branch and multi-project access with strong tenant isolation.
- Roles: Super Admin, Owner, Director, Project Manager, Site Engineer, QS, Procurement, Storekeeper, Accountant, HR, QA, Safety, Subcontractor, Client and Auditor.
- Implement all functional modules and business rules described in the attached "Bangladesh Construction Management ERP — A to Z Master Plan & Development Prompts" document. Treat developer/unit-sales functions as optional extension.
- Prioritize Phase 1 MVP, then implement phases sequentially. Do not claim unfinished features are complete.

For each module deliver, in this exact order:
1) business requirements, entities, permission matrix, state transitions and acceptance criteria;
2) migrations with constraints, indexes, FKs and tenant/project scopes;
3) Eloquent models, relationships, factories and seeders;
4) policies, Form Requests, service/action classes and database transactions;
5) controllers, named routes, Blade views, Bootstrap 5.3 UI, filters and pagination;
6) approval workflows, audit logs, notifications and exports;
7) PHPUnit/Pest feature and unit tests including tenant isolation and edge cases;
8) demo data and setup commands;
9) short manual QA checklist and completion status.

Important rules:
- Never invent VAT/TDS/VDS rates; use date-effective settings and accountant-reviewed rules.
- Never use float for money. Use DECIMAL for amounts and quantities.
- Approved document versions and posted journal entries must be immutable; use reversal/adjustment workflows.
- Enforce permissions and company/project scope on every server action, not just menus.
- Handle partial PO delivery, returns, bill deductions, retention and advance recovery.
- Do not implement fake buttons, placeholder pages or silently skip modules.
- Use database transactions, idempotency and concurrency protection for inventory and payments.
- Use secure private storage for documents and scoped download endpoints.
- Provide complete executable code file by file, with exact paths. If too long, stop at a coherent checkpoint and explicitly identify the next files to generate.

Start by producing: complete module map, ERD (Mermaid), route map, RBAC matrix, phased backlog and the initial Laravel project setup. Then implement Phase 1 in small tested increments, starting with companies/projects/permissions.
```

## 16. MODULE-BY-MODULE IMPLEMENTATION PROMPT TEMPLATE

```text
Continue the Bangladesh Construction ERP project. Implement MODULE: [MODULE NAME] fully, using the established architecture and existing migrations. First inspect existing code and identify dependencies. Provide migrations, models, relationships, policies, requests, services, controllers, routes, Blade/Bootstrap screens, approval flow, seeders and tests. Enforce tenant/project scope, preserve financial auditability, and cover Bangladesh units/BDT/Bangla labels. Include acceptance tests and exact artisan commands. Do not overwrite completed files blindly. Finish with a change log, security checklist, and next module recommendation.
```

### Recommended module execution order
1. Auth, tenants, RBAC, settings, audit logs.
2. Company, branch, projects, project team.
3. Items, units, suppliers, cost codes.
4. BOQ, rate analysis, approved budgets.
5. Material requisitions and approvals.
6. RFQ, quotation comparison, purchase orders.
7. GRN, inventory ledger, transfers and issues.
8. Daily progress, WBS, milestones.
9. Measurement book, RA bills, variations, retention.
10. Project expenses, payments and cashbook.
11. Subcontractors, HR, attendance, payroll.
12. Equipment, fuel, maintenance.
13. QA/QC, HSE, drawing/document control.
14. Double-entry accounts, tax configuration, financial reports.
15. Portals, integrations, PWA, advanced analytics.
16. Optional real-estate developer sales extension.

## 17. Deployment & maintenance checklist
- PHP 8.3+, supported Laravel 12 dependencies, MySQL backups, HTTPS and proper document root.
- Production `APP_DEBUG=false`, strong `APP_KEY`, environment secrets, correct storage/queue/scheduler configuration.
- `composer install --no-dev --optimize-autoloader`, migration plan with backups, `php artisan optimize` where suitable.
- Queue workers supervised, cron scheduler, monitoring, email/SMS retry and error alerts.
- Security headers, least privilege database user, rate limiting, MFA for sensitive roles, patch policy.
- UAT signoff, training, staged rollout, data migration and support agreement.

## 18. Practical launch scope
For a Bangladesh SME construction firm, ship a reliable **Project + BOQ + Procurement + Stock + Daily Progress + Billing + Basic Finance** MVP first. Add accounting, payroll, HSE, equipment and advanced scheduling after the core data and approvals are stable. Maintain a separate feature flag for real-estate unit sales.
