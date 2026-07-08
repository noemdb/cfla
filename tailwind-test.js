const plugin = require('@tailwindcss/typography');
module.exports = {
  content: [{ raw: '<div class="prose prose-sm prose-th:bg-slate-100 prose-td:px-3 prose-td:py-1.5 prose-table:border prose-table:border-collapse prose-th:border prose-table:border-slate-300">test</div>', extension: 'html' }],
  plugins: [plugin],
}
