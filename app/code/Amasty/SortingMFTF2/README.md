# Improved Sorting MFTF2

### Current version of tests is for Magento ЕE 2.3.7 only.

### Release version consists of 12 smoke tests (by one smoke test for each Sorting Option).

### The tests are divided into following groups:
- ImpSort (is used for running of all tests. E.g. vendor/bin/mftf run:group ImpSort -r)
- ImpSortSmoke (is used for running of all smoke tests)
- sortbyposition (is used for running of all tests which check Sort by Position functionality)
- sortbyprodname (is used for running of all tests which check Sort by Product Name functionality)
- sortbyprice (is used for running of all tests which check Sort by Price functionality)
- sortbybestsell (is used for running of all tests which check Sort by Best Sellers functionality)
- sortbymostview (is used for running of all tests which check Sort by Most Viewed functionality)
- sortbynowinwl (is used for running of all tests which check Sort by Now in Wishlists functionality)
- sortbyrevcount (is used for running of all tests which check Sort by Reviews Count functionality)
- sortbytoprat (is used for running of all tests which check Sort by Top Rated functionality)
- sortbybigsave (is used for running of all tests which check Sort by Biggest Saving functionality)
- sortbypricelth (is used for running of all tests which check Sort by Price:low to high functionality)
- sortbypricehtl (is used for running of all tests which check Sort by Price:high to low functionality)
- sortbyrelevance (is used for running of all tests which check Sort by Relevance functionality)
