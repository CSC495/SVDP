/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */
package net.sf.jasperreports.engine.query;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRDataset;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JRQuery;
import net.sf.jasperreports.engine.JRQueryChunk;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JRValueParameter;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.fill.JRFillParameter;
import net.sf.jasperreports.engine.util.JRQueryChunkHandler;
import net.sf.jasperreports.engine.util.JRQueryParser;

/**
 * Base abstract query executer.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRAbstractQueryExecuter.java 5180 2012-03-29 13:23:12Z teodord $
 */
public abstract class JRAbstractQueryExecuter implements JRQueryExecuter
{
	
	protected static final int CLAUSE_POSITION_ID = 0;

	/**
	 * A parameter present in the query.
	 */
	protected static class QueryParameter
	{
		protected static final int COUNT_SINGLE = -1;
		
		private final String name;
		private final int count;
		private final boolean ignoreNulls;
		
		public QueryParameter(String name)
		{
			this(name, COUNT_SINGLE, false);
		}
		
		public QueryParameter(String name, int count)
		{
			this(name, count, false);
		}
		
		public QueryParameter(String name, int count, boolean ignoreNulls)
		{
			this.name = name;
			this.count = count;
			this.ignoreNulls = ignoreNulls;
		}
		
		/**
		 * Decides whether the parameter has multiple values.
		 * 
		 * @return whether the parameter has multiple values.
		 */
		public boolean isMulti()
		{
			return count != COUNT_SINGLE;
		}
		
		/**
		 * Returns the number of parameter values.
		 * 
		 * @return the number of parameter values
		 * @see #isMulti()
		 */
		public int getCount()
		{
			return count;
		}
		
		/**
		 * Returns the name of the report parameter.
		 * 
		 * @return the name of the report parameter
		 */
		public String getName()
		{
			return name;
		}
		
		/**
		 * 
		 * @return a flag indicating if the null values in a multiparameter value should be ignored
		 * 
		 */
		public boolean isIgnoreNulls()
		{
			return ignoreNulls;
		}
	}
	
	/**
	 * Clause function registry.
	 */
	protected final Map<String,JRClauseFunction> clauseFunctions = new HashMap<String,JRClauseFunction>();
	
	private final JasperReportsContext jasperReportsContext;
	private final JRPropertiesUtil propertiesUtil;
	protected final JRDataset dataset;
	private final Map<String,? extends JRValueParameter> parametersMap;
	
	private String queryString;
	
	/**
	 * List of {@link QueryParameter query parameters}.
	 */
	private List<QueryParameter> queryParameters;
	
	private Set<String> parameterClauseStack;
	
	/**
	 * 
	 */
	protected JRAbstractQueryExecuter(
		JasperReportsContext jasperReportsContext, 
		JRDataset dataset, 
		Map<String, ? extends JRValueParameter> parametersMap
		)
	{
		this.jasperReportsContext = jasperReportsContext;
		this.propertiesUtil = JRPropertiesUtil.getInstance(jasperReportsContext);
		this.dataset = dataset;
		this.parametersMap = parametersMap;
		
		queryString = "";
		queryParameters = new ArrayList<QueryParameter>();
	}

	/**
	 * @deprecated Replaced by {@link #JRAbstractQueryExecuter(JasperReportsContext, JRDataset, Map)}.
	 */
	protected JRAbstractQueryExecuter(JRDataset dataset, Map<String, ? extends JRValueParameter> parametersMap)
	{
		this(DefaultJasperReportsContext.getInstance(), dataset, parametersMap);
	}

	/**
	 * 
	 */
	protected JasperReportsContext getJasperReportsContext()
	{
		return jasperReportsContext;
	}

	/**
	 * 
	 */
	protected JRPropertiesUtil getPropertiesUtil()
	{
		return propertiesUtil;
	}

	/**
	 * Registers a clause function.
	 * 
	 * @param id the function ID
	 * @param function the function
	 */
	protected void registerClauseFunction(String id, JRClauseFunction function)
	{
		clauseFunctions.put(id, function);
	}
	
	/**
	 * Unregisters a clause function.
	 * 
	 * @param id the function ID
	 */
	protected void unregisterClauseFunction(String id)
	{
		clauseFunctions.remove(id);
	}
	
	/**
	 * Resolves a clause function ID to a function instance.
	 * 
	 * @param id the function ID
	 * @return the clause function registered for the ID
	 * @throws JRRuntimeException if no function for the ID is found
	 */
	protected JRClauseFunction resolveFunction(String id)
	{
		JRClauseFunction function = clauseFunctions.get(id);
		if (function == null)
		{
			throw new JRRuntimeException("No clause function for id " + id + " found");
		}
		return function;
	}
	
	/**
	 * Parses the query and replaces the parameter clauses by the parameter values and
	 * the parameters by the return value of {@link #getParameterReplacement(String) getParameterReplacement}.
	 *
	 */
	protected void parseQuery()
	{
		parameterClauseStack = new HashSet<String>();
		
		JRQuery query = dataset.getQuery();
		
		if (query != null)
		{
			JRQueryChunk[] chunks = query.getChunks();
			if (chunks != null && chunks.length > 0)
			{
				StringBuffer sbuffer = new StringBuffer();
				for(int i = 0; i < chunks.length; i++)
				{
					JRQueryChunk chunk = chunks[i];
					appendQueryChunk(sbuffer, chunk);
				}

				queryString = sbuffer.toString();
			}
		}
	}


	protected void appendQueryChunk(StringBuffer sbuffer, JRQueryChunk chunk)
	{
		switch (chunk.getType())
		{
			case JRQueryChunk.TYPE_PARAMETER_CLAUSE :
			{
				appendParameterClauseChunk(sbuffer, chunk.getText());
				break;
			}
			case JRQueryChunk.TYPE_PARAMETER :
			{
				appendParameterChunk(sbuffer, chunk.getText());
				break;
			}
			case JRQueryChunk.TYPE_CLAUSE_TOKENS :
			{
				appendClauseChunk(sbuffer, chunk.getTokens());
				break;
			}
			case JRQueryChunk.TYPE_TEXT :
			default :
			{
				appendTextChunk(sbuffer, chunk.getText());
				break;
			}
		}
	}


	protected void appendTextChunk(StringBuffer sbuffer, String text)
	{
		sbuffer.append(text);
	}


	protected void appendParameterChunk(StringBuffer sbuffer, String chunkText)
	{
		String parameterName = chunkText;
		checkParameter(parameterName);

		sbuffer.append(getParameterReplacement(parameterName));
		addQueryParameter(chunkText);
	}


	/**
	 * Records a query parameter.
	 * 
	 * @param parameterName the parameter name
	 * @see #getCollectedParameters()
	 */
	protected void addQueryParameter(String parameterName)
	{
		QueryParameter param = new QueryParameter(parameterName);
		queryParameters.add(param);
	}


	/**
	 * Records a multi-valued query parameter.
	 * 
	 * @param parameterName the parameter name
	 * @param count the value count
	 * @see #getCollectedParameters()
	 * @see QueryParameter#isMulti()
	 */
	protected void addQueryMultiParameters(String parameterName, int count)
	{
		QueryParameter param = new QueryParameter(parameterName, count);
		queryParameters.add(param);
	}


	/**
	 * Records a multi-valued query parameter which ignore null values.
	 * 
	 * @param parameterName the parameter name
	 * @param count the value count
	 * @see #getCollectedParameters()
	 * @see QueryParameter#isMulti()
	 */
	protected void addQueryMultiParameters(String parameterName, int count, boolean ignoreNulls)
	{
		QueryParameter param = new QueryParameter(parameterName, count, ignoreNulls);
		queryParameters.add(param);
	}


	protected void appendParameterClauseChunk(final StringBuffer sbuffer, String chunkText)
	{
		String parameterName = chunkText;
		checkParameter(parameterName);
		
		if (!parameterClauseStack.add(parameterName))
		{
			throw new JRRuntimeException("The query contains circularly nested parameter clauses starting with " + parameterName);
		}
		
		try
		{
			Object parameterValue = getParameterValue(parameterName);
			String clauseText = String.valueOf(parameterValue);
			JRQueryChunkHandler nestedChunkHandler = new JRQueryChunkHandler()
			{
				public void handleParameterChunk(String text)
				{
					appendParameterChunk(sbuffer, text);
				}

				public void handleParameterClauseChunk(String text)
				{
					appendParameterClauseChunk(sbuffer, text);
				}

				public void handleTextChunk(String text)
				{
					appendTextChunk(sbuffer, text);
				}

				public void handleClauseChunk(String[] tokens)
				{
					appendClauseChunk(sbuffer, tokens);
				}
			};
			JRQueryParser.instance().parse(clauseText, nestedChunkHandler);
		}
		finally
		{
			parameterClauseStack.remove(parameterName);
		}
	}


	/**
	 * Handles a {@link JRQueryChunk#TYPE_CLAUSE_TOKENS clause query chunk}.
	 * <p>
	 * The default implementation considers the first token as a
	 * {@link JRClauseFunction clause function} ID and delegates the call to the
	 * function.
	 * </p>
	 * <p>
	 * Extending query executers can override this to implement custom query clause handling.
	 * </p>
	 * 
	 * @param sbuffer the query text buffer
	 * @param clauseTokens clause tokens
	 * @see #registerClauseFunction(String, JRClauseFunction)
	 * @throws JRRuntimeException if there is no first token or no clause function is found for the ID
	 */
	protected void appendClauseChunk(final StringBuffer sbuffer, String[] clauseTokens)
	{
		JRClauseTokens tokens = new JRClauseTokens(clauseTokens);
		String id = tokens.getToken(CLAUSE_POSITION_ID);
		if (id == null)
		{
			throw new JRRuntimeException("Query clause ID/first token missing");
		}
		
		JRClauseFunction function = resolveFunction(id);
		applyClause(function, tokens, sbuffer);
	}

	
	protected void applyClause(JRClauseFunction function, JRClauseTokens tokens, final StringBuffer sbuffer)
	{
		function.apply(tokens, new JRQueryClauseContext()
		{
			public void addQueryMultiParameters(String parameterName, int count)
			{
				addQueryMultiParameters(parameterName, count, false);
			}

			public void addQueryMultiParameters(String parameterName, int count, boolean ignoreNulls)
			{
				JRAbstractQueryExecuter.this.addQueryMultiParameters(parameterName, count, ignoreNulls);
			}

			public void addQueryParameter(String parameterName)
			{
				JRAbstractQueryExecuter.this.addQueryParameter(parameterName);
			}

			public JRValueParameter getValueParameter(String parameterName)
			{
				return JRAbstractQueryExecuter.this.getValueParameter(parameterName);
			}

			public StringBuffer queryBuffer()
			{
				return sbuffer;
			}
		});
	}

	
	/**
	 * Returns the parsed query string with the parameter clauses replaced by the parameter values and 
	 * the parameters replaced by {@link #getParameterReplacement(String) getParameterReplacement}.
	 * 
	 * @return the parsed query string
	 */
	protected String getQueryString()
	{
		return queryString;
	}
	
	
	/**
	 * Returns the list of parameter names in the order in which they appear in the query.
	 * 
	 * @return the list of parameter names
	 */
	protected List<String> getCollectedParameterNames()
	{
		List<String> parameterNames = new ArrayList<String>(queryParameters.size());
		for (Iterator<QueryParameter> it = queryParameters.iterator(); it.hasNext();)
		{
			QueryParameter param = it.next();
			parameterNames.add(param.getName());
		}
		return parameterNames;
	}
	
	
	/**
	 * Returns the list of {@link QueryParameter query parameters} in the order in which they appear in the query.
	 * 
	 * @return the list of query parameters
	 */
	protected List<QueryParameter> getCollectedParameters()
	{
		return queryParameters;
	}
	
	
	/**
	 * Returns the value of a fill parameter.
	 * @param parameterName the parameter name
	 * @param ignoreMissing if <code>true</code>, the method will return null for non existing parameters;
	 * 		otherwise, an exception will be thrown if the parameter does not exist
	 * @return the parameter value
	 */
	protected Object getParameterValue(String parameterName, boolean ignoreMissing)
	{
		if (ignoreMissing)
		{
			JRValueParameter parameter = getValueParameter(JRParameter.REPORT_PARAMETERS_MAP, false);
			@SuppressWarnings("unchecked")
			Map<String,Object> parametersMap = (Map<String,Object>)parameter.getValue(); 			
			return parametersMap.get(parameterName);
		}
		
		JRValueParameter parameter = getValueParameter(parameterName, ignoreMissing);
		return parameter == null ? null : parameter.getValue();
	}
	
	
	/**
	 * Returns the value of a fill parameter.
	 * @param parameterName the parameter name
	 * @return the parameter value
	 */
	protected Object getParameterValue(String parameterName)
	{
		return getParameterValue(parameterName, false);
	}
	
	
	/**
	 * 
	 */
	protected boolean parameterHasValue(String parameter)
	{
		JRValueParameter reportParametersMap = getValueParameter(JRParameter.REPORT_PARAMETERS_MAP, false);
		@SuppressWarnings("unchecked")
		Map<String,Object> parametersMap = (Map<String,Object>)reportParametersMap.getValue();  
		return parametersMap.containsKey(parameter);
	}
	
	
	/**
	 * 
	 */
	protected String getStringParameter(String parameter, String property)
	{
		if (parameterHasValue(parameter))
		{
			return (String)getParameterValue(parameter, true);
		}
		else
		{
			return 
				getPropertiesUtil().getProperty(
					dataset.getPropertiesMap(),
					property
					);
		}
	}
	
	
	/**
	 * 
	 */
	protected String getStringParameterOrProperty(String name)
	{
		return getStringParameter(name, name);
	}
	
	
	/**
	 * 
	 */
	protected boolean getBooleanParameter(String parameter, String property, boolean defaultValue)
	{
		if (parameterHasValue(parameter))
		{
			Boolean booleanValue = (Boolean)getParameterValue(parameter, true);
			if (booleanValue == null)
			{
				return getPropertiesUtil().getBooleanProperty(property);
			}
			else
			{
				return booleanValue.booleanValue();
			}
		}
		else
		{
			return 
				getPropertiesUtil().getBooleanProperty(
					dataset.getPropertiesMap(),
					property,
					defaultValue
					);
		}
	}
	
	
	/**
	 * 
	 */
	protected boolean getBooleanParameterOrProperty(String name, boolean defaultValue)
	{
		return getBooleanParameter(name, name, defaultValue);
	}
	
	
	/**
	 * Return a fill parameter from the parameter map.
	 * 
	 * @param parameterName the parameter name
	 * @return the parameter
	 * @deprecated {@link #getValueParameter(String) getValueParameter(String)} should be used instead
	 */
	protected JRFillParameter getParameter(String parameterName)
	{
		JRFillParameter parameter = (JRFillParameter) parametersMap.get(parameterName);
		
		if (parameter == null)
		{
			throw new JRRuntimeException("Parameter \"" + parameterName + "\" does not exist.");
		}
		
		return parameter;
	}

	
	protected void checkParameter(String parameterName)
	{
		if (!parametersMap.containsKey(parameterName))
		{
			throw new JRRuntimeException("Parameter \"" + parameterName + "\" does not exist.");
		}
	}
	
	
	/**
	 * Return a value parameter from the parameters map.
	 * 
	 * @param parameterName the parameter name
	 * @param ignoreMissing if <code>true</code>, the method will return null for non existing parameters;
	 * 		otherwise, an exception will be thrown if the parameter does not exist
	 * @return the parameter
	 */
	protected JRValueParameter getValueParameter(String parameterName, boolean ignoreMissing)
	{
		JRValueParameter parameter = parametersMap.get(parameterName);
		
		if (parameter == null && !ignoreMissing)
		{
			throw new JRRuntimeException("Parameter \"" + parameterName + "\" does not exist.");
		}
		
		return parameter;
	}

	
	/**
	 * Return a value parameter from the parameters map.
	 * 
	 * @param parameterName the parameter name
	 * @return the parameter
	 */
	protected JRValueParameter getValueParameter(String parameterName)
	{
		return getValueParameter(parameterName, false);
	}

	
	/**
	 * Returns the replacement text for a query parameter.
	 * 
	 * @param parameterName the parameter name
	 * @return the replacement text
	 * @see JRQueryChunk#TYPE_PARAMETER
	 */
	protected abstract String getParameterReplacement(String parameterName);
}
